<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Tagihan extends CI_Model {

	function getAllData($kls = '')
	{
		$this->datatables->select('siswa.nis AS id, siswa.foto_siswa AS foto, siswa.nama_siswa AS name, siswa.nis AS nis, siswa.jk_siswa AS sex, siswa.tmp_lahir AS tempat, siswa.tgl_lahirsiswa AS tanggal, siswa.ortu_wali AS orangtua_wali, siswa.telp_siswa AS telpon, siswa.alamat_siswa AS alamat, siswa.tgl_masuk AS tanggal_masuk, siswa.thn_ajaran AS tahun_ajaran, COALESCE(kelas.nama_kelas, "Belum Diatur") AS nama_kelas, siswa.status_siswa AS status');
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

	function getRiwayatData($filter = array())
	{
		$this->datatables->select("t.id_tagihan AS id, DATE_FORMAT(t.tgl_pembayaran, '%d-%m-%Y %H:%i') as waktu_bayar, s.nama_siswa as nama, s.nis as nis, COALESCE(k.nama_kelas, 'Belum Diatur') as kelas, j.nama_tagihan as jenis_tagihan, j.nominal_tagihan as nominal, t.id_pemasukan");
		$this->datatables->from("tagihan_siswa t");
		$this->datatables->join("siswa s", "t.nis = s.nis");
		$this->datatables->join("jenis_tagihan j", "t.kode_tagihan = j.kode_tagihan");
		$this->datatables->join("kelas k", "s.id_kelas = k.id_kelas", "left");
		$this->datatables->where("t.status", "Lunas");

		if(!empty($filter['jenis']) && !empty($filter['tanggal'])){
            if($filter['jenis'] == 'hari'){
                $this->datatables->where('DATE(t.tgl_pembayaran)', $filter['tanggal']);
            } elseif($filter['jenis'] == 'bulan'){
                $this->datatables->where("DATE_FORMAT(t.tgl_pembayaran, '%Y-%m') = ", $filter['tanggal']);
            } elseif($filter['jenis'] == 'tahun'){
                $this->datatables->where("YEAR(t.tgl_pembayaran)", $filter['tanggal']);
            }
		}

		$btn = '<center>
			<a target="_blank" href="'.base_url('Tagihan/Cetak_Bukti/$1').'" class="btn btn-abu-tua btn-xs">
				<i class="fa fa-print"></i> Cetak Bukti
			</a>
		</center>';

		$this->datatables->add_column('view', $btn, 'id');

		return $this->datatables->generate();
	}
}
