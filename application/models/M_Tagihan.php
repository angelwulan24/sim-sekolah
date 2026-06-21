<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Tagihan extends CI_Model {

	function getAllData($kls = '')
	{
		$this->datatables->select('siswa.id, siswa.name, siswa.nis, siswa.status, siswa.sex, siswa.orangtua_wali, siswa.telpon, siswa.tempat, siswa.tanggal, siswa.alamat, siswa.foto, siswa.tanggal_masuk, siswa.tahun_ajaran, kelas.nama as nama_kelas');
		$this->datatables->from('siswa');
		$this->datatables->join('kelas', 'siswa.kelas = kelas.id', 'left');

		if ($kls !== null && $kls !== '') {
			$this->datatables->where('siswa.kelas', $kls);
		}


		$btn = '<center>
			<a href="'.base_url('Tagihan/Detail/$1').'" class="btn btn-warning btn-xs">
				<i class="fa fa-list"></i> Cek Tagihan
			</a>
		</center>';

		$this->datatables->add_column('view', $btn, 'id');

		return $this->datatables->generate();
	}
}
