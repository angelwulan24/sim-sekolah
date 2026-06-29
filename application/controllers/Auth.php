<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct(){
		parent::__construct();

		$this->load->library('form_validation');
        $this->load->library('Wa_gateway');
	}

	public function index(){

			if ($this->session->userdata('id')){
			if($this->session->userdata('role') == 3){
				redirect('StudentArea','refresh');
			}
			redirect('Beranda','refresh');
		}

		$this->form_validation->set_rules('email','Username / Email','trim|required');
		$this->form_validation->set_rules('password','Password','trim|required');

		if ($this->form_validation->run() == false) {
				$data['title']	= 'Halaman Login | SIM ';
				$this->load->view('v_Login',$data);
		} 
		else {
			$this->_login();
		}
	}

	private function _login(){

		$email = $this->input->post('email');
		$password = $this->input->post('password');

		$user = $this->db->get_where('users', ['email'=> $email])->row_array();

		if ($user){
			if (password_verify($password, $user['password'])){
				$data = array(
					'id' 	=> $user['id_users'],
					'role'	=> $user['role']
				);
				$this->session->set_userdata( $data );
				if($user['role'] == 3){
					redirect('StudentArea','refresh');
				} else {
					redirect('Beranda','refresh');
				}
			}
			else{
				$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>  Password Salah</div>');
				redirect($this->uri->segment(1),'refresh');	
			}
		}
		else{
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>  Username / Email tidak terdaftar</div>');
			redirect($this->uri->segment(1),'refresh');	
		}
	}

	function Simpan(){

		$id   = $this->session->userdata('id');
		$pass = $this->input->post('lama');
		$baru = password_hash($this->input->post('baru'), PASSWORD_DEFAULT);
		$user = $this->db->get_where('users', ['id_users'=> $id])->row_array();

		if (password_verify($pass, $user['password'])){
				$this->db->where('id_users',$id);
				$this->db->update('users',array('password'=>$baru));
				$data['status'] = true;
		}else{
			$data['status'] = false;
		}
		 $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function update_foto(){
		$id = $this->session->userdata('id');
		$user = $this->db->get_where('users', ['id_users' => $id])->row_array();
		
		if($user['role'] == 3) {
			show_error('Akses ditolak. Siswa tidak diizinkan mengubah foto profil sendiri.', 403);
			return;
		}

		$config['upload_path']      = './assets/dist/img/';
		$config['allowed_types']    = 'gif|jpg|png|jpeg';
		$config['max_size']         = 2048;
		$config['file_name']        = 'admin';
		$config['overwrite']        = TRUE;
		
		$this->load->library('upload', $config);
		if ($this->upload->do_upload('foto')) {
			if (!empty($user['gambar']) && $user['gambar'] != 'user.png' && $user['gambar'] != $this->upload->data('file_name')) {
				if (file_exists('./assets/dist/img/' . $user['gambar'])) {
					unlink('./assets/dist/img/' . $user['gambar']);
				}
			}
			$this->db->where('id_users', $id);
			$this->db->update('users', ['gambar' => $this->upload->data('file_name')]);
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> Berhasil mengubah foto profil</div>');
		} else {
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> Gagal upload foto: ' . strip_tags($this->upload->display_errors()) . '</div>');
		}
		
		if(isset($_SERVER['HTTP_REFERER'])){
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect('Beranda');
		}
	}

	public function registrasi (){

		if ($this->session->userdata('id')){
			redirect('Beranda','refresh');
		}

		$this->form_validation->set_rules('name','Nama','trim|required');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password','Password','trim|required|min_length[6]|matches[password2]');
		$this->form_validation->set_rules('password2','Kofirmasi Password','trim|required|matches[password]');

		if ($this->form_validation->run() == false) {
				$data['title']	= 'Halaman Login | SIM ';
				$this->load->view('v_Registrasi',$data);
		} 
		else {
			$email = filter_string($this->input->post('email',true));
			$data = array(
					'nama_users' => filter_string($this->input->post('name',true)),
					'email' 	 => $email,
					'gambar'	 => 'user.png',
					'password'	 => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
					'role'		 => 2
			);

			$this->M_General->insert('users',$data);

			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> Berhasil Mendaftar, silakan login</div>');
			redirect($this->uri->segment(1),'refresh');
		}
	}




	public function logout(){
		$this->session->sess_destroy();
		redirect('Auth');
	}
}