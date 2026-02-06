<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Baju extends CI_Model {

	function Detail($id){
		$this->datatables->select('id,time as tanggal, waktu,nominal');
		$this->datatables->from('baju');
		$this->datatables->where('id_siswa',$id);
		//$this->datatables->add_column('view','<center><a href="javascript:void(0)" onclick="Ubah($1)" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a> <a href="javascript:void(0)" onclick="Hapus($1)" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</a></center> ','id');
		//$this->datatables->edit_column('nominal','Rp. $1','nominal');
		return $this->datatables->generate();
	}
	

}