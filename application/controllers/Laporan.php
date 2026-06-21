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
		$filter = array(
			'jenis'   => $this->input->post('jenis'),
			'tanggal' => $this->input->post('tanggal')
		);
		echo $this->mod->getAllData($filter);
	}

	function Cetak(){
		if (ob_get_length() > 0) {
			ob_clean();
		}
		ini_set('display_errors', '0');

		if (is_cli()) {
			$_POST['jenis_cetak'] = 'bulan';
			$_POST['print_bulan'] = '2026-04';
		}
		$jenis_cetak = $this->input->post('jenis_cetak');

		if ($jenis_cetak == 'bulan') {
			$print_bulan = $this->input->post('print_bulan'); // format YYYY-MM
			$awal = $print_bulan . '-01';
			$akhir = date('Y-m-t', strtotime($awal)); // last day of month

			$this->db->where('tanggal >=', $awal);
			$this->db->where('tanggal <=', $akhir);
		} elseif ($jenis_cetak == 'tahun') {
			$print_tahun = $this->input->post('print_tahun'); // format YYYY
			$awal = $print_tahun . '-01-01';
			$akhir = $print_tahun . '-12-31';

			$this->db->where('tanggal >=', $awal);
			$this->db->where('tanggal <=', $akhir);
		} else {
			$awal = $this->input->post('awal');
			$akhir = $this->input->post('akhir');

			$this->db->where('tanggal >=', $awal);
			$this->db->where('tanggal <=', $akhir);
		}

		$a = $this->db->get('laporan')->result();
		$this->mod->Cetak_periode($a, $awal, $akhir);
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