<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentArea extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('M_Siswa');
		$this->load->model('M_General');
		// We can't call is_login() here IF is_login() redirects to StudentArea, 
		// because of infinite loop potential if logic is flawed.
		// But based on my plan: if role==3 && class!=StudentArea -> redirect StudentArea.
		// So if class==StudentArea, it continues.
		is_login();
	}

	public function index(){
		// Get logged in user
		$user_id = $this->session->userdata('id');
		$user_role = $this->session->userdata('role');
		$user_email = $this->db->get_where('users', ['id' => $user_id])->row()->email;

		// Assuming email is nis@student.sim
		// Extract NIS
		$parts = explode('@', $user_email);
		if(count($parts) > 1 && $parts[1] == 'student.sim'){
			$nis = $parts[0];
		} else {
			// Fallback or error
			$nis = ''; 
		}

		// Get Student Data
		$student = $this->db->get_where('siswa', ['nis' => $nis])->row();

		if(!$student){
			echo "Data Siswa tidak ditemukan untuk akun ini.";
			return;
		}

		$data['title'] = 'Area Siswa | SIM Sekolah';
		$data['student'] = $student;
		
		// Load Bills (Tagihan)
		// Need logic to fetch bills. Example: SPP, Ujian, etc.
		// Using simple queries for now based on tables seeing in SQL
		// spp table: id_siswa (smallint), time, bulan, nominal
		
		$data['spp'] = $this->db->get_where('spp', ['id_siswa' => $student->id])->result();
		
		// For sidebar active state
		$this->parents = 'Tagihan'; 
		
		// Use Template
		$this->load->library('template');
		$this->template->views('v_student_area', $data);
	}
}
