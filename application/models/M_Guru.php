<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Guru extends CI_Model {

	function getAllData(){
		$this->datatables->select('id,name,sex,nip,bidang,alamat,number,status,foto');
		$this->datatables->from('guru');

		if ($this->session->userdata('role') == 1) {
			$btn = '<center><a href="javascript:void(0)" onclick="Ubah($1)" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a></center>';
		} else {
			$btn = '';
		}

		$this->datatables->add_column('view', $btn, 'id');
		return $this->datatables->generate();
	}
	

}

/* End of file m_Menu_1.php */
/* Location: ./application/models/m_Menu_1.php */