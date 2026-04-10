<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Buku extends CI_Model {

	function Detail($d){
		$this->datatables->select("id,DATE_FORMAT(time,'%d-%m-%Y - %H:%i:%s WIB') as tanggal,tahun_ajaran,nominal");
		$this->datatables->where('id_siswa',$d);
		$this->datatables->from('buku');
		return $this->datatables->generate();
	}
	

}