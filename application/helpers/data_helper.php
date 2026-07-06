<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    function get_kelas ($id){
        $ci=& get_instance();
        $q = $ci->db->query("SELECT nama_kelas FROM kelas WHERE id_kelas='$id'")->row_array();
        return isset($q['nama_kelas']) ? $q['nama_kelas'] : '';
    }

    function get_nama ($id){
        $ci=& get_instance();
        $q = $ci->db->query("SELECT name FROM temp WHERE id='$id'")->row_array();
        return isset($q['name']) ? $q['name'] : '';
    }

    function get_siswa($id){
        $ci=& get_instance();
        $q = $ci->db->query("SELECT nama_siswa FROM siswa WHERE nis='$id'")->row_array();
        return isset($q['nama_siswa']) ? $q['nama_siswa'] : '';
    }

    function get_guru ($id){
        $ci=& get_instance();
        $q = $ci->db->query("SELECT nama_guru FROM guru WHERE NUPTK='$id'")->row_array();
        return isset($q['nama_guru']) ? $q['nama_guru'] : '';
    }