<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lainnya extends CI_Controller {

	private $parents = 'Lainnya';
	private $icon	 = 'fa fa-money';
	var $table 		 = 'pemasukan';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb('Pemasukan Uang '.$this->parents,$this->parents);

		$data['title']	= 'Pemasukan '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pemasukan '.$this->parents;
		$data['icon']	= $this->icon;

		$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		echo $this->mod->getAllData();
	}

	function getDetail(){

		header('Content-Type:application/json');
		$id = $this->input->post('tgl');
		echo $this->mod->getDetailData($id);
	}

	function Detail(){
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Lainnya'));
		$this->breadcrumb->append_crumb('Detail Pemasukan Uang '.$this->parents,$this->parents);

		$data['title']	= 'Detail Pemasukan '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Detail Pemasukan '.$this->parents;
		$data['icon']	= $this->icon;
		$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);
	}

	function Simpan(){

		$total = filter_string($this->input->post('nominal',TRUE));

		$insert = array(
			'nominal_pemasukan' => $total,
			'tgl_pemasukan'     => date('Y-m-d H:i:s'),
			'ket_pemasukan'     => filter_string($this->input->post('keterangan',TRUE))
		);

		$this->M_General->insert($this->table,$insert);
		$data['status'] = TRUE;

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function edit($id){
		$data = $this->M_General->getByID($this->table,'id_pemasukan',$id,'DESC')->row();
		echo json_encode($data);
	}

	function Ubah(){
		$id = $this->input->post('id');
		$update = array(
			'nominal_pemasukan' => filter_string($this->input->post('nominal',TRUE)),
			'ket_pemasukan'     => filter_string($this->input->post('keterangan',TRUE))
		);
		$this->M_General->update($this->table,$update,'id_pemasukan',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Hapus($id){
		$this->M_General->delete($this->table,'id_pemasukan',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

}