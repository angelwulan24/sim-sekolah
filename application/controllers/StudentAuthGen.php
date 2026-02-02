<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentAuthGen extends CI_Controller {

	public function index(){
        echo "<h1>Generasi Akun Siswa...</h1>";
        
        $students = $this->db->get('siswa')->result();
        $count = 0;
        
        foreach($students as $s){
            $nis = $s->nis;
            if(empty($nis)) continue;
            
            $email = $nis . '@student.sim';
            
            // Check if user exists
            $cek = $this->db->get_where('users', ['email' => $email])->num_rows();
            
            if($cek == 0){
                // Create User
                $data = array(
                    'name' => $s->name,
                    'email' => $email,
                    'password' => password_hash($nis, PASSWORD_DEFAULT), // Pass is NIS
                    'role' => 3,
                    'active' => '1',
                    'gambar' => 'user.png' // Default image
                );
                
                $this->db->insert('users', $data);
                echo "Akun dibuat untuk: " . $s->name . " ($email)<br>";
                $count++;
            }
        }
        
        echo "<br>Selesai. Total akun dibuat: $count";
	}
}
