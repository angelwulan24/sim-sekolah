<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller {

	private $parents = 'Siswa';
	private $icon	 = 'fa fa-users';
	var $table 		 = 'siswa';
	private $filename = "import_data"; 

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

	public function getData()
	{
		$kelas = $this->input->post('is_kelas', true); // XSS filter optional
		$json  = $this->mod->getAllData($kelas);

		$this->output
			->set_content_type('application/json')
			->set_output($json);
	}


	public function edit($id){
		$data = $this->M_General->getByID($this->table,'id',$id,'id')->row();
		echo json_encode($data);
	}

	function import(){

		$upload = $this->M_General->upload_file($this->filename);
	
		if ($upload['status'] == true){  
		include APPPATH.'third_party/PHPExcel/PHPExcel.php';
		$excelreader = new PHPExcel_Reader_Excel2007();
		$loadexcel = $excelreader->load('excel/'.$this->filename.'.xlsx');
		$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);
		
		$data = array();
		$numrow = 1;
		foreach($sheet as $row){
			if($numrow > 1){
				// Kita push (add) array data ke variabel data
				array_push($data, array(
					'name'=>$row['A'],
					'nis'=>$row['B'],
					'tempat'=>$row['C'],
					'tanggal'=>$row['D'],
					'sex'=>$row['E'],
					'orangtua_wali'=>$row['F'],
					'alamat'=>$row['G'],
					'status'=>$row['H'],
					'kelas'=>$row['I'],
				));

                // Create User Account with NIS as username
                $this->_create_account($row['B'], $row['A'], $row['D']);
			}
			
			$numrow++;
		}
		$this->M_General->insert_multiple($data);
		
        $data['status'] = TRUE;
        $this->M_General->delete('siswa','kelas','0');
    	}
    	else{
    		$data['status'] = FALSE;
    	}
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Simpan(){
        $insert = array(
                    'name'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'sex'		=> $this->input->post('gender',TRUE),
                    'nis' 		=> $this->input->post('nis',TRUE),
                    'kelas' 	=> $this->input->post('kelas',TRUE),
                    'tempat'	=> filter_string($this->input->post('tempat',TRUE)),
                    'tanggal'	=> filter_string($this->input->post('tanggal',TRUE)),
                    'alamat'	=> filter_string($this->input->post('alamat',TRUE)),
                    'status'	=> filter_string($this->input->post('status',TRUE)),
                    'telpon'    => filter_string($this->input->post('telpon',TRUE)),
                    'agama'     => filter_string($this->input->post('agama',TRUE)),
                    'orangtua_wali'		=> filter_string($this->input->post('orangtua_wali',TRUE)),
                    'tanggal_masuk' => filter_string($this->input->post('tanggal_masuk',TRUE)),
                    'tahun_ajaran'	=> filter_string($this->input->post('tahun_ajaran',TRUE))
                );

        if(!empty($_FILES['foto']['name'])){
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $config['upload_path']   = './assets/images/siswa/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']     = strtolower(str_replace(' ', '_', $insert['name'])) . '_' . time() . '.' . $ext;
            $this->load->library('upload');
            $this->upload->initialize($config);
            if($this->upload->do_upload('foto')){
                $uploadData = $this->upload->data();
                $insert['foto'] = $uploadData['file_name'];
            }
        }

        $this->M_General->insert($this->table,$insert);
        
        // Create User Account with NIS as username
        $this->_create_account($insert['nis'], $insert['name'], $insert['tanggal']);

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Ubah(){
        $id = $this->input->post('id');
        $old_data = $this->M_General->getByID($this->table,'id',$id,'id')->row();
        
        $insert = array(
                    'name'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'sex'		=> $this->input->post('gender',TRUE),
                    'nis' 		=> $this->input->post('nis',TRUE),
                    'kelas' 	=> $this->input->post('kelas',TRUE),
                    'tempat'	=> filter_string($this->input->post('tempat',TRUE)),
                    'tanggal'	=> filter_string($this->input->post('tanggal',TRUE)),
                    'alamat'	=> filter_string($this->input->post('alamat',TRUE)),
                    'status'	=> filter_string($this->input->post('status',TRUE)),
                    'telpon'    => filter_string($this->input->post('telpon',TRUE)),
                    'agama'     => filter_string($this->input->post('agama',TRUE)),
                    'orangtua_wali'		=> filter_string($this->input->post('orangtua_wali',TRUE)),
                    'tanggal_masuk' => filter_string($this->input->post('tanggal_masuk',TRUE)),
                    'tahun_ajaran'	=> filter_string($this->input->post('tahun_ajaran',TRUE))
                );

        if(!empty($_FILES['foto']['name'])){
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $config['upload_path']   = './assets/images/siswa/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']     = strtolower(str_replace(' ', '_', $insert['name'])) . '_' . time() . '.' . $ext;
            $this->load->library('upload');
            $this->upload->initialize($config);
            if($this->upload->do_upload('foto')){
                $uploadData = $this->upload->data();
                $insert['foto'] = $uploadData['file_name'];
            }
        }
        $this->M_General->update($this->table,$insert,'id',$id);

        // Update User Account if NIS changed
        if($old_data && $old_data->nis != $insert['nis']){
            $this->db->where('email', $old_data->nis);
            $this->db->update('users', array('email' => $insert['nis'], 'name' => $insert['name']));
        } else {
            // Update name in user account even if NIS didn't change
            $this->db->where('email', $insert['nis']);
            $this->db->update('users', array('name' => $insert['name']));
        }

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Hapus($id){
        $data_siswa = $this->M_General->getByID($this->table,'id',$id,'id')->row();
        if($data_siswa){
            // Delete User Account using NIS as identity in email column
            $this->db->where('email', $data_siswa->nis);
            $this->db->delete('users');
            
            // Delete photo file if exists
            if (!empty($data_siswa->foto) && file_exists('./assets/images/siswa/' . $data_siswa->foto)) {
                unlink('./assets/images/siswa/' . $data_siswa->foto);
            }
        }

		$this->M_General->delete($this->table,'id',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

    // Helper to create account
    private function _create_account($nis, $name, $dob){
        $username = $nis; 
        $cek = $this->db->get_where('users', ['email' => $username])->num_rows();
        
        // Format password from DOB (yyyy-mm-dd -> ddmmyy)
        $password_raw = date('dmy', strtotime($dob));

        if($cek == 0){
            $user = array(
                'name' 		=> $name,
                'email' 	=> $username, // Using email column for NIS as username
                'gambar'	=> 'user.png',
                'password'	=> password_hash($password_raw, PASSWORD_DEFAULT),
                'role'		=> 3, // Student
                'active'	=> '1'
            );
            $this->db->insert('users', $user);
        }
    }
}