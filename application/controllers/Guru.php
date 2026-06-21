<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru extends CI_Controller {

	private $parents = 'Guru';
	private $icon	 = 'fa fa-graduation-cap';
	var $table 		 = 'guru';

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
		$this->breadcrumb->append_crumb($this->parents,$this->parents);

		$data['title']	= $this->parents.' | SIM Sekolah ';
		$data['judul']	= $this->parents;
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		echo $this->mod->getAllData();
	}


	public function edit($id){
		$data = $this->M_General->getByID($this->table,'NUPTK',$id,'NUPTK')->row();
		echo json_encode($data);
	}

	function Simpan(){
        $insert = array(
                    'nama_guru'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'jk_guru'		=> $this->input->post('gender',TRUE),
                    'NUPTK' 		=> $this->input->post('nip',TRUE),
                    'bidang_studi'	=> filter_string($this->input->post('bidang',TRUE)),
                    'alamat_guru'	=> filter_string($this->input->post('alamat',TRUE)),
                    'status_guru'	=> filter_string($this->input->post('status',TRUE)),
                    'telp_guru'	    => filter_string($this->input->post('telepon',TRUE))
                );

        if(!empty($_FILES['foto']['name'])){
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $config['upload_path']   = './assets/images/guru/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']     = strtolower(str_replace(' ', '_', $insert['nama_guru'])) . '_' . time() . '.' . $ext;
            $this->load->library('upload');
            $this->upload->initialize($config);
            if($this->upload->do_upload('foto')){
                $uploadData = $this->upload->data();
                $insert['foto_guru'] = $uploadData['file_name'];
            }
        }

        $this->M_General->insert($this->table,$insert);
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Ubah(){
        $insert = array(
                    'nama_guru'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'jk_guru'		=> $this->input->post('gender',TRUE),
                    'NUPTK' 		=> $this->input->post('nip',TRUE),
                    'bidang_studi'	=> filter_string($this->input->post('bidang',TRUE)),
                    'alamat_guru'	=> filter_string($this->input->post('alamat',TRUE)),
                    'status_guru'	=> filter_string($this->input->post('status',TRUE)),
                    'telp_guru'	    => filter_string($this->input->post('telepon',TRUE))
                );

        if(!empty($_FILES['foto']['name'])){
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $config['upload_path']   = './assets/images/guru/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']     = strtolower(str_replace(' ', '_', $insert['nama_guru'])) . '_' . time() . '.' . $ext;
            $this->load->library('upload');
            $this->upload->initialize($config);
            if($this->upload->do_upload('foto')){
                $uploadData = $this->upload->data();
                $insert['foto_guru'] = $uploadData['file_name'];
            }
        }
        $this->M_General->update($this->table,$insert,'NUPTK',$this->input->post('id'));
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Hapus($id){
		$this->M_General->delete($this->table,'NUPTK',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function DetailGaji($id){
		$this->load->helper('data');
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Guru'));
		$this->breadcrumb->append_crumb('Detail Gaji Guru',$this->parents);

		$data['title']	= 'Detail Gaji Guru | SIM Sekolah ';
		$data['judul']	= 'Detail Gaji Guru';
		$data['icon']	= $this->icon;
		$data['isi']	= $this->M_General->getByID('gaji','NUPTK',$id,'DESC')->result();

	    $this->template->views('Backend/'.$this->parents.'/v_Detail_Gaji',$data);
	}
}