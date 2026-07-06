<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Tagihan extends CI_Model {

	function getAllData($kls = '')
	{
		$this->datatables->select('siswa.nis AS id, siswa.foto_siswa AS foto, siswa.nama_siswa AS name, siswa.nis AS nis, siswa.jk_siswa AS sex, siswa.tmp_lahir AS tempat, siswa.tgl_lahirsiswa AS tanggal, siswa.ortu_wali AS orangtua_wali, siswa.telp_siswa AS telpon, siswa.alamat_ssiwa AS alamat, siswa.tgl_masuk AS tanggal_masuk, siswa.thn_ajaran AS tahun_ajaran, COALESCE(kelas.nama_kelas, "Belum Diatur") AS nama_kelas, siswa.status_siswa AS status');
		$this->datatables->from('siswa');
		$this->datatables->join('kelas', 'siswa.id_kelas = kelas.id_kelas', 'left');

		if ($kls !== null && $kls !== '') {
			$this->datatables->where('siswa.id_kelas', $kls);
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
