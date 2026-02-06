<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

	private $parents = 'Laporan';
	private $icon	 = 'fa fa-line-chart';
	var $table 		 = '';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah','Beranda');
		$this->breadcrumb->append_crumb($this->parents.' Kas',$this->parents);

		$data['title']	= $this->parents.' Kas | SIM Sekolah';
		$data['judul']	= $this->parents.' Kas';
		$data['icon']	= $this->icon;

	$this->template->views('/Backend/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		echo $this->mod->getAllData();
	}

	function Cetak(){
		 $awal = $this->input->post('awal');
		 $akhir = $this->input->post('akhir');

		 $this->db->where('tanggal >=',$awal);
		 $this->db->where('tanggal <=',$akhir); // Changed to <= for correct range
		 $a = $this->db->get('laporan')->result();

		 $this->mod->Cetak_periode($a,$awal,$akhir);
	}

		function Detail($id){

		$this->load->helper('data');
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents.' Kas',base_url('Laporan'));
		$this->breadcrumb->append_crumb('Detail Laporan Kas',$this->parents);

		$data['title']	= 'Detail '.$this->parents.' Kas | SIM Sekolah ';
		$data['judul']	= 'Detail '.$this->parents.' Kas';
		$data['icon']	= $this->icon;
		$data['isi']	= $this->M_General->get_laporan($id);

	$this->template->views('Backend/v_Detail',$data);

	}


	public function Cetak_detail($id){
		$data = $this->M_General->get_laporan($id);
        if (empty($data) || empty($data['tanggal'])) {
            show_error('Data Laporan tidak ditemukan.', 404);
            return;
        }
		$this->mod->Cetak_detail($data);
	}

}

/* End of file Beranda.php */
/* Location: ./application/controllers/Beranda.php */