<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ujian extends CI_Controller {

	private $parents = 'Ujian';
	private $icon	 = 'fa fa-money';
	var $table 		 = 'ujian';

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

	function getRiwayat($id){
		header('Content-Type:application/json');
		$this->datatables->select('id, time, periode, nominal');
		$this->datatables->from('ujian');
		$this->datatables->where('id_siswa', $id);
		$this->datatables->add_column('aksi','<a href="'.base_url('Ujian/CetakBukti/$1').'" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-print"></i> Cetak</a>','id');
		echo $this->datatables->generate();
	}

	function getUjian(){
		header('Content-Type:application/json');
		$n = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 2")->row_array();
		echo json_encode($n['nominal']);
	}

	function GetSiswaName($id){
		header('Content-Type:application/json');
		$siswa = $this->db->query("SELECT name FROM siswa WHERE id = '$id'")->row_array();
		echo json_encode($siswa);
	}

	function Detail($id){
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Ujian'));
		$this->breadcrumb->append_crumb('Detail Pembayaran Ujian',$this->parents);

		$data['title']	= 'Pembayaran Uang '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pembayaran Uang '.$this->parents;
		$data['icon']	= $this->icon;
		
		// Get current year
		$tahun_sekarang = date('Y');
		if (date('m') < 7) {
			$tahun_sekarang = $tahun_sekarang - 1;
		}
		
		// Get student class information for history
		$siswa = $this->db->query("SELECT s.id, k.nama FROM siswa s LEFT JOIN kelas k ON s.kelas = k.id WHERE s.id = '$id'")->row();
		
		// Get nominal ujian
		$nominal = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 2")->row_array();
		
		// Determine how many years of history based on class
		$kelas_num = ($siswa && $siswa->nama) ? (int)preg_replace('/[^0-9]/', '', $siswa->nama) : 1;
		$max_history = max(0, $kelas_num - 1);
		
		// Generate available years
		$tahun_list = array();
		for($i = 0; $i <= $max_history; $i++) {
			$tahun_list[] = $tahun_sekarang - $i;
		}
		
		$selected_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : $tahun_sekarang;
		
		// Generate all periode ujian (Ganjil, Genap)
		$periode_array = array('Ganjil', 'Genap');
		$isi = array();
		
		foreach($periode_array as $periode){
			$periode_label = $periode . '-' . $selected_tahun;
			$cek = $this->db->query("SELECT id, time FROM ujian WHERE id_siswa = '$id' AND periode = '$periode_label'")->row();
			
			$obj = new stdClass();
			$obj->periode = $periode;
			$obj->periode_label = $periode_label;
			$obj->nominal = $nominal['nominal'];
			$obj->time = $cek ? $cek->time : null;
			$obj->status = $cek ? 'Lunas' : 'Belum Lunas';
			$obj->id = $cek ? $cek->id : null;
			
			$isi[] = $obj;
		}
		
		$data['isi'] = $isi;
		$data['tahun_list'] = $tahun_list;
		$data['selected_tahun'] = $selected_tahun;
		$data['tahun_sekarang'] = $tahun_sekarang;
		$data['id_siswa'] = $id;

		$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);

	}
	function Simpan(){

		$id = $this->input->post('id_siswa',TRUE);
		$bln = filter_string($this->input->post('bulan',TRUE));
		$cek = $this->db->query("SELECT id FROM ujian WHERE id_siswa = '$id' AND periode = '$bln' ")->num_rows();

		if ($cek > 0){
			$data['status'] = FALSE;
    	}
    	else{

    		$nominal = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 2")->row_array();
    		$total = $nominal['nominal'];
    		$insert = array(
	                    'id_siswa'	=> $id,
	                    'time'	   => waktu(),
	                    'periode'	=> $bln,
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
		
		$data = $this->db->query("
			SELECT u.*, s.name, s.nis 
			FROM ujian u 
			JOIN siswa s ON u.id_siswa = s.id 
			WHERE u.id = '$id'
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
		$pdf->Cell(0, 5, 'BUKTI PEMBAYARAN UJIAN', 0, 1, 'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('TIMES','',10);
		$pdf->Cell(40, 6, 'No. Transaksi', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, 'UJIAN-'.$data->id, 0, 1);
		
		$pdf->Cell(40, 6, 'Tanggal', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, date('d-m-Y H:i', strtotime($data->time)), 0, 1);
		
		$pdf->Cell(40, 6, 'Nama Siswa', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->name, 0, 1);
		
		$pdf->Cell(40, 6, 'NIS', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->nis, 0, 1);
		
		$pdf->Cell(40, 6, 'Periode Ujian', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->periode, 0, 1);
		
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