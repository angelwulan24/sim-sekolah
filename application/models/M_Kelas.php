<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Kelas extends CI_Model {

	function getAllData(){
		$this->datatables->select('id,nama,wali,keterangan');
		$this->datatables->from('kelas');

		$btn_detail = '<a href="javascript:void(0)" onclick="Detail($1)" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Detail</a>';
		$btn_ubah = '';
		if ($this->session->userdata('role') == 1) {
			$btn_ubah = ' <a href="javascript:void(0)" onclick="Ubah($1)" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a>';
		}
		
		$this->datatables->add_column('view', '<center>' . $btn_detail . $btn_ubah . '</center>', 'id');
		return $this->datatables->generate();
	}
	

}