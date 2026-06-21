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
			 
			 // Send WhatsApp Notification
			 $this->load->library('Wa_gateway');
			 $this->wa_gateway->send_payment_confirmation($id, "Uang Ujian - " . $bln, $total);

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
		$pdf->Cell(190, 7, 'BUKTI PEMBAYARAN UJIAN', 0, 1, 'C');
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
		$pdf->Cell(35, 6, 'Periode Ujian', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->periode, 0, 1);

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