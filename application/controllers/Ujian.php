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
		$siswa = $this->db->query("SELECT s.id, k.nama FROM siswa s JOIN kelas k ON s.kelas = k.id WHERE s.id = '$id'")->row();
		
		// Get nominal ujian
		$nominal = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 2")->row_array();
		
		// Determine how many years of history based on class
		$kelas_num = (int)preg_replace('/[^0-9]/', '', $siswa->nama);
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

	        $insert = $this->M_General->insert($this->table,$insert);
	         $this->M_General->update_kas('kas_masuk',$total);
	        $data['status'] = TRUE;
    		
    	}
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

}