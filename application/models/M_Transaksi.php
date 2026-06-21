<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Transaksi extends CI_Model {

	function getAllData(){
		$this->datatables->select("j.kode_tagihan, j.nama_tagihan, j.nominal_tagihan, j.tenggat_waktu, j.tahun_ajaran, COALESCE(k.nama_kelas, 'Semua Kelas') AS kelas");
		$this->datatables->from('jenis_tagihan AS j');
		$this->datatables->join('kelas AS k', 'j.id_kelas = k.id_kelas', 'left');

		if ($this->session->userdata('role') == 1) {
			$btn = '<center><a href="javascript:void(0)" onclick="Ubah(\'$1\')" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a> <a href="javascript:void(0)" onclick="Hapus(\'$1\')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</a></center>';
		} else {
			$btn = '';
		}

		$this->datatables->add_column('view', $btn, 'kode_tagihan');
		return $this->datatables->generate();
	}
	

}

/* End of file m_Menu_1.php */
/* Location: ./application/models/m_Menu_1.php */