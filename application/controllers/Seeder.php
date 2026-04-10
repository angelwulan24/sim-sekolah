<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seeder extends CI_Controller {

    public function index() {
        if (!is_cli()) {
            echo "Must be run from CLI";
            return;
        }

        echo "Checking for existing users...\n";

        // Check for Kepsek
        $kepsek = $this->db->get_where('users', ['email' => 'kepsek@gmail.com'])->row();
        if (!$kepsek) {
            $data = [
                'name' => 'Kepala Yayasan',
                'email' => 'kepsek@gmail.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 2,
                'active' => '1',
                'gambar' => 'user.png'
            ];
            $this->db->insert('users', $data);
            echo "Created Kepala Yayasan user (kepsek@gmail.com)\n";
        } else {
            echo "Kepala Yayasan user already exists (kepsek@gmail.com)\n";
        }

        // Check for Siswa
        $siswa = $this->db->get_where('users', ['email' => 'siswa@gmail.com'])->row();
        if (!$siswa) {
            $data = [
                'name' => 'Siswa Teladan',
                'email' => 'siswa@gmail.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'role' => 3,
                'active' => '1',
                'gambar' => 'user.png'
            ];
            $this->db->insert('users', $data);
            echo "Created Siswa user (siswa@gmail.com)\n";
        } else {
            echo "Siswa user already exists (siswa@gmail.com)\n";
        }
    }
}
