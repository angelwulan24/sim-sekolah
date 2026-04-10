<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SPP extends CI_Controller {

	private $parents = 'SPP';
	private $icon	 = 'fa fa-money';
	var $table 		 = 'spp';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		//$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
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

	function getSPP(){
		header('Content-Type:application/json');
		$n = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 1")->row_array();
		echo json_encode($n['nominal']);
	}

	function Detail($id){
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('SPP'));
		$this->breadcrumb->append_crumb('Detail Pembayaran SPP',$this->parents);

		$data['title']	= 'Pembayaran Uang '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pembayaran Uang '.$this->parents;
		$data['icon']	= $this->icon;
		
		// Get student class information
		$siswa = $this->db->query("SELECT k.id, k.nama FROM siswa s LEFT JOIN kelas k ON s.kelas = k.id WHERE s.id = '$id'")->row();
		
		// Get nominal SPP
		$nominal = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 1")->row_array();
		
		// Get current year
		$tahun_sekarang = date('Y');
		$selected_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : $tahun_sekarang;
		
		// Determine how many years of history based on class
		// Kelas 1 = 0 history, Kelas 2 = 1 history, Kelas 3 = 2 history, etc
		$kelas_num = ($siswa && $siswa->nama) ? (int)preg_replace('/[^0-9]/', '', $siswa->nama) : 1;
		$max_history = max(0, $kelas_num - 1);
		
		// Get all months with payment status
		$bulan_array = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		$isi = array();
		
		foreach($bulan_array as $bulan){
			$bulan_label = $bulan . '-' . $selected_tahun;
			$cek = $this->db->query("SELECT id, time FROM spp WHERE id_siswa = '$id' AND bulan = '$bulan_label'")->row();
			
			$obj = new stdClass();
			$obj->bulan = $bulan_label;
			$obj->nominal = $nominal['nominal'];
			$obj->time = $cek ? $cek->time : null;
			$obj->status = $cek ? 'Lunas' : 'Belum Lunas';
			$obj->id = $cek ? $cek->id : null;
			
			$isi[] = $obj;
		}
		
		$data['isi'] = $isi;
		$data['selected_tahun'] = $selected_tahun;
		$data['tahun_sekarang'] = $tahun_sekarang;
		$data['max_history'] = $max_history;
		$data['id_siswa'] = $id;

	$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);

	}

	function Simpan(){
		$id = $this->input->post('id',TRUE);
		$bln = filter_string($this->input->post('bulan',TRUE));
		$cek = $this->db->query("SELECT id FROM spp WHERE id_siswa = '$id' AND bulan = '$bln' ")->num_rows();

		if ($cek > 0){
			$data['status'] = FALSE;
    	}
    	else{
    		$total = filter_string($this->input->post('harga',TRUE));
    		$insert = array(
	                    'id_siswa'	=> $id,
	                    'time'	   => waktu(),
	                    'bulan'		=> $bln,
	                    'nominal'	=> $total
	                );

	        $this->db->insert($this->table,$insert);
			$data['id_pembayaran'] = $this->db->insert_id();
	        
			$this->M_General->update_kas('kas_masuk',$total);
	        $data['status'] = TRUE;
    	}
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function CetakBukti($id){
		$this->load->library('pdf');
		$this->load->helper('data');
		
		$spp = $this->db->query("
			SELECT s.*, si.name, si.nis 
			FROM spp s 
			JOIN siswa si ON s.id_siswa = si.id 
			WHERE s.id = '$id'
		")->row();

		if(!$spp) {
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
		$pdf->Cell(0, 5, 'BUKTI PEMBAYARAN SPP', 0, 1, 'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('TIMES','',10);
		$pdf->Cell(40, 6, 'No. Transaksi', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, 'SPP-'.$spp->id, 0, 1);
		
		$pdf->Cell(40, 6, 'Tanggal', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, date('d-m-Y H:i', strtotime($spp->time)), 0, 1);
		
		$pdf->Cell(40, 6, 'Nama Siswa', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $spp->name, 0, 1);
		
		$pdf->Cell(40, 6, 'NIS', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $spp->nis, 0, 1);
		
		$pdf->Cell(40, 6, 'Pembayaran Bulan', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $spp->bulan, 0, 1);
		
		$pdf->Ln(5);
		$pdf->SetFont('TIMES','B',12);
		$pdf->Cell(40, 8, 'TOTAL BAYAR', 0, 0);
		$pdf->Cell(5, 8, ':', 0, 0);
		$pdf->Cell(0, 8, 'Rp. '.number_format($spp->nominal, 0, ',', '.'), 0, 1);
		
		// Footer / Signature
		$pdf->Ln(10);
		$pdf->SetFont('TIMES','',10);
		
		$pdf->Cell(80); // Spacer
		$pdf->Cell(0, 5, 'Pati, '.date('d F Y'), 0, 1, 'C');
		$pdf->Cell(80);
		$pdf->Cell(0, 5, 'Bendahara,', 0, 1, 'C');
		
		$pdf->Ln(15);
		$pdf->Cell(80);
		$pdf->Cell(0, 5, '('.$this->session->userdata('nama').')', 0, 1, 'C');
		
		$pdf->Output();
	}

}