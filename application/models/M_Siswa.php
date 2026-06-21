<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Siswa extends CI_Model {

	function getAllData($kls = '')
	{
		$this->datatables->select('siswa.nis_siswa, siswa.nama_siswa, siswa.jk_siswa, siswa.agama_siswa, siswa.status_siswa, siswa.ortu_wali, siswa.telp_siswa, siswa.tempat_lahirsiswa, siswa.tgl_lahirsiswa, siswa.alamat_ssiwa, siswa.foto_siswa, siswa.tgl_masuk, siswa.thn_ajaran, kelas.nama_kelas');
		$this->datatables->from('siswa');
		$this->datatables->join('kelas', 'siswa.id_kelas = kelas.id_kelas', 'left');

		if ($kls !== null && $kls !== '') {
			$this->datatables->where('siswa.id_kelas', $kls);
		}


		if ($this->session->userdata('role') == 1) {
			$btn = '<center>
				<a href="javascript:void(0)" onclick="Ubah(\'$1\')" class="btn btn-warning btn-xs">
					<i class="fa fa-pencil"></i> Ubah
				</a>
				<a href="javascript:void(0)" onclick="Hapus(\'$1\')" class="btn btn-danger btn-xs">
					<i class="fa fa-trash"></i> Hapus
				</a>
			</center>';
		} else {
			$btn = '';
		}

		$this->datatables->add_column('view', $btn, 'nis_siswa');



		return $this->datatables->generate();
	}
}