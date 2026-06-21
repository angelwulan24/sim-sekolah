<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Siswa extends CI_Model {

	function getAllData($kls = '')
	{
		$this->datatables->select('siswa.id, siswa.name, siswa.nis, siswa.status, siswa.sex, siswa.agama, siswa.orangtua_wali, siswa.telpon, siswa.tempat, siswa.tanggal, siswa.alamat, siswa.foto, siswa.tanggal_masuk, siswa.tahun_ajaran, kelas.nama as nama_kelas');
		$this->datatables->from('siswa');
		$this->datatables->join('kelas', 'siswa.kelas = kelas.id', 'left');

		if ($kls !== null && $kls !== '') {
			$this->datatables->where('siswa.kelas', $kls);
		}


		if ($this->session->userdata('role') == 1) {
			$btn = '<center>
				<a href="javascript:void(0)" onclick="Ubah($1)" class="btn btn-warning btn-xs">
					<i class="fa fa-pencil"></i> Ubah
				</a>
				<a href="javascript:void(0)" onclick="Hapus($1)" class="btn btn-danger btn-xs">
					<i class="fa fa-trash"></i> Hapus
				</a>
			</center>';
		} else {
			$btn = '';
		}

		$this->datatables->add_column('view', $btn, 'id');



		return $this->datatables->generate();
	}
}