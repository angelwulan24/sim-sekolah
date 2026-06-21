<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SyncAccounts extends CI_Controller {

    public function __construct(){
        parent::__construct();
        is_login();
        // Only allow admins (or specific roles) to run this
        if($this->session->userdata('role') != 1 && $this->session->userdata('role') != 2){
            die('Unauthorized');
        }
    }

    public function index(){
        $students = $this->db->get('siswa')->result();
        $count_created = 0;
        $count_skipped = 0;

        foreach($students as $s){
            $username = $s->nis;
            
            // Skip if username is empty
            if(empty($username)){
                $count_skipped++;
                continue;
            }

            // Check if account already exists
            $cek = $this->db->get_where('users', ['email' => $username])->num_rows();

            if($cek == 0){
                // Format password from DOB (yyyy-mm-dd -> ddmmyy)
                // Default to '010100' if date is invalid or empty
                $dob = $s->tanggal;
                $password_raw = (!empty($dob) && $dob != '0000-00-00') ? date('dmy', strtotime($dob)) : '010100';

                $user = array(
                    'name' 		=> $s->name,
                    'email' 	=> $username, 
                    'gambar'	=> 'user.png',
                    'password'	=> password_hash($password_raw, PASSWORD_DEFAULT),
                    'role'		=> 3, // Student
                    'active'	=> '1'
                );
                $this->db->insert('users', $user);
                $count_created++;
            } else {
                $count_skipped++;
            }
        }

        echo "<h3>Proses Sinkronisasi Selesai</h3>";
        echo "<p>Total Siswa diperiksa: ".count($students)."</p>";
        echo "<p>Akun Baru Dibuat: $count_created</p>";
        echo "<p>Akun Sudah Ada / Dilewati: $count_skipped</p>";
        echo "<br><a href='".base_url('Beranda')."'>Kembali ke Dashboard</a>";
    }
}
