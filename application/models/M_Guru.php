<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Guru extends CI_Model {

	function getAllData(){
		$this->datatables->select('NUPTK, nama_guru, jk_guru, bidang_studi, alamat_guru, telp_guru, status_guru, foto_guru');
		$this->datatables->from('guru');

		if ($this->session->userdata('role') == 1) {
			$btn = '<center><a href="javascript:void(0)" onclick="DetailGaji(\'$1\')" class="btn btn-abu-tua btn-xs"><i class="fa fa-money"></i> Detail Gaji</a> <a href="javascript:void(0)" onclick="Ubah(\'$1\')" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a> <a href="javascript:void(0)" onclick="Hapus(\'$1\')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</a></center>';
		} elseif ($this->session->userdata('role') == 2) {
			$btn = '<center><a href="javascript:void(0)" onclick="DetailGaji(\'$1\')" class="btn btn-abu-tua btn-xs"><i class="fa fa-money"></i> Detail Gaji</a></center>';
		} else {
			$btn = '';
		}

		$this->datatables->add_column('view', $btn, 'NUPTK');
		return $this->datatables->generate();
	}
	

}

/* End of file m_Menu_1.php */
/* Location: ./application/models/m_Menu_1.php */