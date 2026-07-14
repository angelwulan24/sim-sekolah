<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Lainnya extends CI_Model {

	function getAllData($filter = array()){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		$this->datatables->select("id_pemasukan AS id, DATE_FORMAT(tgl_pemasukan,'%d-%m-%Y') AS Tgl, nominal_pemasukan AS Total, ket_pemasukan AS keterangan");
		$this->datatables->from('pemasukan');
		$this->datatables->where("NOT EXISTS (SELECT 1 FROM tagihan_siswa WHERE tagihan_siswa.id_pemasukan = pemasukan.id_pemasukan)", NULL, FALSE);
		
		if(!empty($filter['jenis']) && !empty($filter['tanggal'])){
            if($filter['jenis'] == 'hari'){
                $this->datatables->where('DATE(tgl_pemasukan)', $filter['tanggal']);
            } elseif($filter['jenis'] == 'bulan'){
                $this->datatables->where("DATE_FORMAT(tgl_pemasukan, '%Y-%m') = ", $filter['tanggal']);
            } elseif($filter['jenis'] == 'tahun'){
                $this->datatables->where("YEAR(tgl_pemasukan)", $filter['tanggal']);
            }
		}

		$btn = '<center><a href="javascript:void(0)" onclick="Ubah(\'$1\')" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Ubah</a> <a href="javascript:void(0)" onclick="Hapus(\'$1\')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</a></center>';
		$this->datatables->add_column('view', $btn, 'id');
		
		return $this->datatables->generate();
	}

	function getDetailData($detail =''){
		// Filter by DATE(tgl_pemasukan) instead of deprecated sekarang field
		$this->datatables->select("id_pemasukan AS id, DATE_FORMAT(tgl_pemasukan,'%d-%m-%Y') AS Tgl, nominal_pemasukan AS nominal, ket_pemasukan AS keterangan");
		$this->datatables->from('pemasukan');
		$this->datatables->where("NOT EXISTS (SELECT 1 FROM tagihan_siswa WHERE tagihan_siswa.id_pemasukan = pemasukan.id_pemasukan)", NULL, FALSE);
		if (!empty($detail)) {
			$this->datatables->where('DATE(tgl_pemasukan)', $detail);
		}
		return $this->datatables->generate();
	}
	

}