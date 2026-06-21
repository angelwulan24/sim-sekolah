<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Baju extends CI_Controller {

	private $parents = 'Baju';
	private $icon	 = 'fa fa-square';
	var $table 		 = 'baju';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
		$this->load->helper('data');
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
		echo $this->mod->getSiswa($kls); 
	}

	function getDetail($id){
		header('Content-Type:application/json');
		echo $this->mod->Detail($id);
	}

	function getBaju(){
		header('Content-Type:application/json');
		$n = $this->db->query("SELECT nominal FROM pembayaran WHERE nama = 'Uang Baju'")->row_array();
		echo json_encode($n['nominal']);
	}

	function UpdateData(){
		$id_sis = $this->input->post('id');
		$sis = get_siswa($id_sis);
		$tang = $this->input->post('tanggal');

		$quer = $this->db->query("SELECT id FROM baju WHERE id_siswa ='$id_sis' AND waktu ='$tang'")->num_rows();

		if ($quer > 0){
			$this->db->query("UPDATE baju SET nominal = '0' WHERE id_siswa='$id_sis' AND waktu = '$tang'");
			$n = $this->db->query("SELECT nominal FROM pembayaran WHERE nama = 'Uang Baju' ")->row_array();
			$this->M_General->update_kas('kas_keluar',$n['nominal']);
			$data['status'] = TRUE;
			    		$insert = array(
	                    'nominal'	=> $n['nominal'],
	                    'sekarang'	=> sekarang(),
	                    'time'	   => waktu(),
	                    'keterangan'	=>'Ubah Uang Baju dengan Nama '.$sis
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
		$this->breadcrumb->append_crumb($this->parents,base_url('Baju'));
		$this->breadcrumb->append_crumb('Detail Pembayaran Uang Baju',$this->parents);

		$data['title']	= 'Pembayaran Uang '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pembayaran Uang '.$this->parents;
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);

	}

	function Simpan(){
		$harga = $this->input->post('harga');
		$id = $this->input->post('id_siswa');

		$udh = $this->db->query("SELECT id FROM baju WHERE id_siswa = '$id' ")->num_rows();
		
		if ($udh > 0) {
			$data['status'] = FALSE;
			$data['message'] = "Uang Baju sudah lunas/dibayarkan.";
		} else {
			$data_insert = array(
				'waktu'    => waktu(),
				'nominal'  => $harga,
				'time'	   => waktu(),
				'id_siswa' => $id,
			);
			$this->db->insert('baju', $data_insert);
			$this->M_General->update_kas('kas_masuk', $harga);
			
			// Send WhatsApp Notification
			$this->load->library('Wa_gateway');
			$this->wa_gateway->send_payment_confirmation($id, "Uang Baju", $harga);

			$data['status'] = TRUE;
		}
		
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function CetakBukti($id){
		$this->load->library('pdf');
		$this->load->helper('data');
		
		$data = $this->db->query("
			SELECT b.*, s.name, s.nis 
			FROM baju b 
			JOIN siswa s ON b.id_siswa = s.id 
			WHERE b.id = '$id'
		")->row();

		if(!$data) {
			show_error('Data pembayaran tidak ditemukan');
			return;
		}

		$pdf = new FPDF('p','mm','A4');
        $pdf->SetMargins(10, 10, 10);
		$pdf->AddPage();
		
        // Header
        $pdf->Image(FCPATH.'assets/dist/img/MI.png', 10, 12, 28);
        $pdf->SetFont('TIMES','B',12);
        $pdf->Cell(28); 
        $pdf->Cell(162, 6, 'YAYASAN PENDIDIKAN ISLAM', 0, 1, 'C');
        $pdf->Cell(28);
        $pdf->SetFont('TIMES','B',16);
        $pdf->Cell(162, 8, 'MADRASATUL QURAN DAAR EL-MUFLIHIN', 0, 1, 'C');
        $pdf->Cell(28);
        $pdf->SetFont('TIMES','',10);
        $pdf->Cell(162, 5, 'Perum Cikande Permai Blok G7/01 RT. 06/4 Kec. Cikande Kab. Serang', 0, 1, 'C');
        $pdf->Cell(28);
        $pdf->Cell(162, 5, 'Telp.0823-1138-8825, email: midaarelmuflihin@gmail.com', 0, 1, 'C');
        
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, 42, 200, 42);
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, 43, 200, 43);
		
		$pdf->Ln(8);
		$pdf->SetFont('TIMES','B',12);
		$pdf->Cell(190, 7, 'BUKTI PEMBAYARAN UANG BAJU', 0, 1, 'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'Nama Siswa', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->SetFont('TIMES','B',11);
		$pdf->Cell(0, 6, $data->name, 0, 1);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'NIS', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->nis, 0, 1);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'Tanggal Bayar', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, date('d-m-Y', strtotime($data->waktu)), 0, 1);

		$pdf->Ln(5);
		$pdf->SetFont('TIMES','B',12);
		$pdf->Cell(35, 8, 'TOTAL BAYAR', 0, 0);
		$pdf->Cell(5, 8, ':', 0, 0);
		$pdf->Cell(0, 8, 'Rp. '.number_format($data->nominal, 0, ',', '.'), 0, 1);
		
		$pdf->Ln(15);
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(130);
		$pdf->Cell(60, 5, 'Cikande Permai, '.date('d F Y'), 0, 1, 'C');
		$pdf->Cell(130);
		$pdf->Cell(60, 5, 'Bendahara Sekolah,', 0, 1, 'C');
		
		$pdf->Ln(20);
		$pdf->Cell(130);
        $pdf->SetFont('TIMES','B',11);
		$pdf->Cell(60, 5, 'Nani Nuraeni S.Pd', 0, 1, 'C');
		
		$pdf->Output();
	}

}