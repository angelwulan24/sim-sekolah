<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends CI_Controller {

	private $parents = 'Kelas';
	private $icon	 = 'fa fa-institution ';
	var $table 		 = 'kelas';

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
		$data['guru']	=$this->db->query("SELECT nama_guru AS name, NUPTK AS nip FROM guru")->result();

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		echo $this->mod->getAllData();
	}


	public function edit($id){
		$data = $this->M_General->getByID($this->table,'id_kelas',$id,'id_kelas')->row();
		echo json_encode($data);
	}

	function Pindah(){

		$siswa = $this->input->post('id');
		$kelas = $this->input->post('kelas');

		$ar = array();

		if(!empty($siswa)){
			foreach ($siswa as $i => $key){

				array_push($ar,array(
					'id_kelas' => $kelas,
					'nis_siswa' => $key
				));
			}

		$this->db->update_batch('siswa',$ar,'nis_siswa');
		}

		redirect($this->uri->segment(1),'refresh');
	}

	function Detail($id){

		$this->load->helper('data');

		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Kelas'));
		$this->breadcrumb->append_crumb('Detail Kelas',$this->parents);

		$data['title']	= 'Data '.$this->parents.' '.get_kelas($id).' | SIM Sekolah ';
		$data['judul']	=  $this->parents.' '.get_kelas($id);
		$data['icon']	= $this->icon;
		$data['siswa']	=$this->db->query("SELECT * FROM siswa WHERE id_kelas = $id")->result();

		$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);


	}

	function Simpan(){
        $insert = array(
                    'nama_kelas'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'NUPTK'		    => $this->input->post('wali',TRUE),
                    'ket_kelas'	    => filter_string($this->input->post('keterangan',TRUE))
                );

        $this->M_General->insert($this->table,$insert);
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Ubah(){
        $insert = array(
                    'nama_kelas'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'NUPTK'		    => $this->input->post('wali',TRUE),
                    'ket_kelas'	    => filter_string($this->input->post('keterangan',TRUE))
                );
        $this->M_General->update($this->table,$insert,'id_kelas',$this->input->post('id'));
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
	function Kenaikan(){
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Kelas'));
		$this->breadcrumb->append_crumb('Kenaikan Kelas',$this->parents);

		$data['title']	= 'Kenaikan Kelas | SIM Sekolah ';
		$data['judul']	= 'Kenaikan Kelas Otomatis';
		$data['icon']	= $this->icon;
		$data['kelas']	= $this->db->get('kelas')->result();

		$this->template->views('Backend/'.$this->parents.'/v_Kenaikan',$data);
	}

	function ProsesKenaikan(){
		$dari = $this->input->post('dari_kelas');
		$ke = $this->input->post('ke_kelas');
		
		if (!empty($dari) && !empty($ke) && $dari != $ke) {
			if ($ke == 'lulus') {
                // Delete previous alumni
                $alumni_siswa = $this->db->get_where('siswa', array('status_siswa' => 'Alumni'))->result();
                if (!empty($alumni_siswa)) {
                    $alumni_user_ids = array();
                    foreach ($alumni_siswa as $al) {
                        if (!empty($al->id_users)) {
                            $alumni_user_ids[] = $al->id_users;
                        }
                        if (!empty($al->foto_siswa) && file_exists('./assets/images/siswa/' . $al->foto_siswa)) {
                            unlink('./assets/images/siswa/' . $al->foto_siswa);
                        }
                    }
                    if (!empty($alumni_user_ids)) {
                        $this->db->where_in('id_users', $alumni_user_ids);
                        $this->db->delete('users');
                    }
                    $this->db->where('status_siswa', 'Alumni');
                    $this->db->delete('siswa');
                }

				$data = array(
					'status_siswa' => 'Alumni',
					'id_kelas'     => NULL
				);
			} else {
				$data = array(
					'id_kelas' => $ke
				);
			}

			$this->db->where('id_kelas', $dari);
			$this->db->update('siswa', $data);

            $count = $this->db->affected_rows();
            if ($count > 0) {
                $this->session->set_flashdata('success', 'Berhasil memproses ' . $count . ' siswa.');
            } else {
                $this->session->set_flashdata('error', 'Tidak ada data siswa yang diupdate. Pastikan kelas asal memiliki siswa.');
            }
		} else {
            $this->session->set_flashdata('error', 'Kelas asal dan tujuan tidak boleh sama atau kosong.');
        }
		
		redirect('Kelas/Kenaikan');
	}

	function Hapus($id){
		// Check if class has students
		$siswa_count = $this->db->get_where('siswa', array('id_kelas' => $id))->num_rows();
		if ($siswa_count > 0) {
			$data['status'] = FALSE;
			$data['error'] = 'Kelas tidak dapat dihapus karena masih memiliki siswa aktif.';
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
			return;
		}
		
		$this->M_General->delete($this->table,'id_kelas',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

}