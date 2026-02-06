<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tanggal extends CI_Controller {

	private $parents = 'Tanggal';
	private $icon	 = 'fa fa-calendar';
	var $table 		 = 'tanggal';

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
		$this->breadcrumb->append_crumb($this->parents.' Merah',$this->parents);

		$data['title']	= $this->parents.' Merah/Hari Libur | SIM Sekolah ';
		$data['judul']	= $this->parents.' Merah/Hari Libur';
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		echo $this->mod->getAllData();
	}

	function edit($id){
		header('Content-Type:application/json');
		$data = $this->M_General->getByID($this->table, 'id', $id, 'DESC')->row();
		echo json_encode($data);
	}

	function Simpan(){
        $insert = array(
                    'tgl'			=> filter_string($this->input->post('tanggal',TRUE)),
                    'keterangan'	=> filter_string($this->input->post('keterangan',TRUE))
                );

        $insert = $this->M_General->insert($this->table,$insert);
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function Ubah(){
		$data = array(
			'tgl'        => filter_string($this->input->post('tanggal', TRUE)),
			'keterangan' => filter_string($this->input->post('keterangan', TRUE))
		);

		$this->M_General->update(
			$this->table,
			$data,
			'id',
			$this->input->post('id')
		);

		echo json_encode(['status' => TRUE]);
	}	

	public function Hapus($id){
		$this->M_General->delete($this->table,'id',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
}