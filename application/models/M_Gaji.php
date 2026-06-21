<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Gaji extends CI_Model {

	function getAllData(){
		$this->datatables->select('NUPTK AS id, nama_guru AS name, jk_guru AS sex, NUPTK AS nip, telp_guru AS number');
		$this->datatables->from('guru');
		
		$this->datatables->add_column('view','<center><a href="javascript:void(0)" onclick="Detail(\'$1\')" class="btn btn-pastel-gaji btn-xs"><i class="fa fa-eye"></i> Detail</a></center> ','id');
		return $this->datatables->generate();
	}

}