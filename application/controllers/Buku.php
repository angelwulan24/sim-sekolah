<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku extends CI_Controller {

	private $parents = 'Buku';
	private $icon	 = 'fa fa-money';
	var $table 		 = 'buku';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
		$this->load->helper('data');
        $this->load->library('Wa_gateway');
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb('Uang '.$this->parents,$this->parents);

		$data['title']	= 'Pembayaran Uang '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pembayaran Uang '.$this->parents;
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		$kls = $this->input->post('is_kelas');
		echo $this->M_General->getSiswa($kls); 
	}

	function getDetail($id){
		header('Content-Type:application/json');
		echo $this->mod->Detail($id);
	}

	function getBuku(){
		header('Content-Type:application/json');
		$n = $this->db->query("SELECT nominal FROM pembayaran WHERE nama = 'Uang Buku'")->row_array();
		echo json_encode($n['nominal']);
	}

	function UpdateData(){
		$id_sis = $this->input->post('id');
		$sis = get_siswa($id_sis);
		$tang = $this->input->post('tanggal');

		$quer = $this->db->query("SELECT id FROM buku WHERE id_siswa ='$id_sis' AND waktu ='$tang'")->num_rows();

		if ($quer > 0){
			$this->db->query("UPDATE buku SET nominal = '0' WHERE id_siswa='$id_sis' AND waktu = '$tang'");
			$n = $this->db->query("SELECT nominal FROM pembayaran WHERE nama = 'Uang Buku' ")->row_array();
			$this->M_General->update_kas('kas_keluar',$n['nominal']);
			$data['status'] = TRUE;
			    		$insert = array(
	                    'nominal'	=> $n['nominal'],
	                    'sekarang'	=> sekarang(),
	                    'time'	   => waktu(),
	                    'keterangan'	=>'Ubah Uang Buku dengan Nama '.$sis
	                );

	        $insert = $this->M_General->insert('lainnya',$insert);
		}
		else{
			$data['status'] = FALSE;	
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Detail($id){
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Buku'));
		$this->breadcrumb->append_crumb('Detail Pembayaran Uang Buku',$this->parents);

		$data['title']	= 'Pembayaran Uang '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pembayaran Uang '.$this->parents;
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);

	}

	function Simpan(){
		$harga = $this->input->post('harga');
		$id = $this->input->post('id_siswa');
		$tahun_ajaran = filter_string($this->input->post('tahun_ajaran',TRUE));

		$udh = $this->db->query("SELECT id FROM buku WHERE id_siswa = '$id' AND tahun_ajaran = '$tahun_ajaran'")->num_rows();
		
		if ($udh > 0) {
			$data['status'] = FALSE;
			$data['message'] = "Uang Buku untuk tahun ajaran " . $tahun_ajaran . " sudah lunas.";
		} else {
			$data_insert = array(
				'waktu'    => waktu(),
				'tahun_ajaran' => $tahun_ajaran,
				'nominal'  => $harga,
				'time'	   => waktu(),
				'id_siswa' => $id,
			);
			$this->db->insert('buku', $data_insert);
			$this->M_General->update_kas('kas_masuk', $harga);
			
			// Send WhatsApp Notification
			$siswa = $this->db->get_where('siswa', ['id' => $id])->row_array();
			if ($siswa && !empty($siswa['telpon'])) {
				$message = "Terima kasih, pembayaran Uang Buku atas nama *" . $siswa['name'] . "* sebesar *" . rupiah($harga) . "* untuk tahun ajaran " . $tahun_ajaran . " telah lunas. \n\nTerima Kasih.";
				//$this->wa_gateway->send($siswa['telpon'], $message);
			}

			$data['status'] = TRUE;
		}
		
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function CetakBukti($id){
		$this->load->library('pdf');
		$this->load->helper('data');
		
		$data = $this->db->query("
			SELECT b.*, s.name, s.nis 
			FROM buku b 
			JOIN siswa s ON b.id_siswa = s.id 
			WHERE b.id = '$id'
		")->row();

		if(!$data) {
			show_error('Data pembayaran tidak ditemukan');
			return;
		}

		$pdf = new FPDF('P','mm','A4');
		$pdf->AddPage();
		
       // Header
       $pdf->Cell(3,5,'',0,1);
       $pdf->Image(base_url().'/assets/dist/img/MI.png', 10, 10,33);
       $pdf->Cell(3,-5,'',0,1);
       $pdf->SetFont('TIMES','B',14);
       $pdf->Cell(189, 5, 'KEMENTRIAN AGAMA REPUBLIK INDONESIA', 0, 1, 'C');
       $pdf->Cell(189, 7, 'KANTOR KEMENTRIAN AGAMA KABUPATEN PATI', 0, 1, 'C');
       $pdf->SetFont('TIMES','B',16);
       $pdf->Cell(192, 7, 'MADRASAH ALIYAH NEGERI PATI', 0, 1, 'C');
       $pdf->SetFont('TIMES','',12);
       $pdf->Cell(189, 5, 'Jl. Ratu kalinyamat Gg. Melati II, Kec. Tayu, Kabupaten Pati', 0, 1, 'C');
       $pdf->Cell(189, 5, 'Telp.(020) 0000000,Fax(020)0000000', 0, 1, 'C');
       $pdf->Cell(189, 5, 'E-mail : madrasahaliyah@gmail.com', 0, 1, 'C');
       $pdf->SetLineWidth(1);
       $pdf->Line(9, 46, 203, 46);
       $pdf->SetLineWidth(0);
       $pdf->Line(9, 47, 203, 47);
		
		$pdf->Cell(3,8,'',0,1);
		
		// Content
		$pdf->SetFont('TIMES','B',11);
		$pdf->Cell(0, 5, 'BUKTI PEMBAYARAN UANG BUKU', 0, 1, 'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('TIMES','',10);
		$pdf->Cell(40, 6, 'No. Transaksi', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, 'BUKU-'.$data->id, 0, 1);
		
		$pdf->Cell(40, 6, 'Tanggal Input', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, date('d-m-Y H:i', strtotime($data->time)), 0, 1);
		
		$pdf->Cell(40, 6, 'Tahun Ajaran', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->tahun_ajaran, 0, 1);
		
		$pdf->Cell(40, 6, 'Nama Siswa', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->name, 0, 1);
		
		$pdf->Cell(40, 6, 'NIS', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->nis, 0, 1);
		
		$pdf->Ln(5);
		$pdf->SetFont('TIMES','B',12);
		$pdf->Cell(40, 8, 'TOTAL BAYAR', 0, 0);
		$pdf->Cell(5, 8, ':', 0, 0);
		$pdf->Cell(0, 8, 'Rp. '.number_format($data->nominal, 0, ',', '.'), 0, 1);
		
       $pdf->SetFont('TIMES','',12);
       $pdf->Cell(125, 35, '', 0, 1);
       $pdf->Cell(125, 35, '', 0, 0);
       $pdf->Cell(55, 5, 'Pati, '.  date('d F Y'), 0, 1);
       $pdf->Cell(125, 5, '', 0, 0);
       $pdf->Cell(35, 5, 'Bendahara,', 0, 1);
       $pdf->Cell(125, 10, '', 0, 0);
       $pdf->Cell(35, 14, '', 0, 1);
       $pdf->Cell(125, 8, '', 0, 0);
       $pdf->Cell(35, 9, '('.$this->session->userdata('nama').')', 0, 0);
		
		$pdf->Output();
	}

}