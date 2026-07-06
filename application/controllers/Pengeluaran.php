<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengeluaran extends CI_Controller {

	private $parents = 'Pengeluaran';
	private $icon	 = 'fa fa-cart-plus';
	var $table 		 = 'pengeluaran';

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
		$this->breadcrumb->append_crumb($this->parents.' Uang',$this->parents);

		$data['title']	= $this->parents.' Lainnya | SIM Sekolah ';
		$data['judul']	= $this->parents.' Lainnya';
		$data['icon']	= $this->icon;

		$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		$filter = array(
			'jenis'   => $this->input->post('jenis'),
			'tanggal' => $this->input->post('tanggal')
		);
		echo $this->mod->getAllData($filter);
	}

	function getDetail(){

		header('Content-Type:application/json');
		$id = $this->input->post('tgl');
		echo $this->mod->getDetailData($id);
	}

	function Detail(){
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Pengeluaran'));
		$this->breadcrumb->append_crumb('Detail Pengeluaran Lainnya',$this->parents);

		$data['title']	= 'Detail '.$this->parents.' Lainnya | SIM Sekolah ';
		$data['judul']	= 'Detail '.$this->parents.' Lainnya';
		$data['icon']	= $this->icon;
		$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);
	}

	function Simpan(){

		$total = filter_string($this->input->post('nominal',TRUE));
		$insert = array(
			'nominal_pengeluaran' => $total,
			'tgl_pengeluaran'     => date('Y-m-d H:i:s'),
			'ket_pengeluaran'     => filter_string($this->input->post('keterangan',TRUE))
		);

		$config['upload_path']	= './assets/images/';
		$config['allowed_types']= 'gif|jpg|png|jpeg';
		$config['max_size']		= 2048;
		$config['encrypt_name']	= TRUE;

		$this->load->library('upload', $config);
		
		if($this->upload->do_upload('bukti')){
			$uploadData = $this->upload->data();
			$insert['bukti'] = $uploadData['file_name'];
		}

		$this->M_General->insert($this->table,$insert);
		$data['status'] = TRUE;

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function edit($id){
		$is_gaji = $this->db->get_where('gaji', ['id_pengeluaran' => $id])->num_rows() > 0;
		if ($is_gaji) {
			echo json_encode(['error' => 'Pembayaran Gaji tidak dapat diubah / diedit secara manual.']);
			return;
		}
		$data = $this->M_General->getByID($this->table,'id_pengeluaran',$id,'DESC')->row();
		echo json_encode($data);
	}

	function Ubah(){
		$id = $this->input->post('id');
		$is_gaji = $this->db->get_where('gaji', ['id_pengeluaran' => $id])->num_rows() > 0;
		if ($is_gaji) {
			$data['status'] = FALSE;
			$data['error'] = 'Pembayaran Gaji tidak dapat diubah secara manual.';
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
			return;
		}

		$update = array(
			'nominal_pengeluaran' => filter_string($this->input->post('nominal',TRUE)),
			'ket_pengeluaran'     => filter_string($this->input->post('keterangan',TRUE))
		);

		if(!empty($_FILES['bukti']['name'])){
			$config['upload_path']	= './assets/images/';
			$config['allowed_types']= 'gif|jpg|png|jpeg';
			$config['max_size']		= 2048;
			$config['encrypt_name']	= TRUE;

			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			
			if($this->upload->do_upload('bukti')){
				$uploadData = $this->upload->data();
				$update['bukti'] = $uploadData['file_name'];
			}
		}

		$this->M_General->update($this->table,$update,'id_pengeluaran',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Hapus($id){
		$is_gaji = $this->db->get_where('gaji', ['id_pengeluaran' => $id])->num_rows() > 0;
		if ($is_gaji) {
			$data['status'] = FALSE;
			$data['error'] = 'Pembayaran Gaji tidak dapat dihapus secara manual.';
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
			return;
		}
		$this->M_General->delete($this->table,'id_pengeluaran',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

}