<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Pengeluaran  extends CI_Model {

	function getAllData($filter = array()){
		$this->datatables->select("id,sekarang,DATE_FORMAT(s.tanggal,'%d-%m-%Y') AS Tgl, s.keterangan, s.nominal AS Total, s.bukti");
		$this->datatables->from('v_pengeluaran_gabungan as s');

		if(!empty($filter['jenis']) && !empty($filter['tanggal'])){
            if($filter['jenis'] == 'hari'){
                $this->datatables->where('DATE(s.tanggal)', $filter['tanggal']);
            } elseif($filter['jenis'] == 'bulan'){
                $this->datatables->where("DATE_FORMAT(s.tanggal, '%Y-%m') = ", $filter['tanggal']);
            } elseif($filter['jenis'] == 'tahun'){
                $this->datatables->where("YEAR(s.tanggal)", $filter['tanggal']);
            }
		}

		return $this->datatables->generate();
	}

	function getDetailData($detail =''){
		$this->datatables->select("id_pengeluaran AS id,DATE_FORMAT(tanggal,'%d-%m-%Y - %H:%i:%s WIB') AS Tgl,nominal_pengeluaran AS nominal,ket_pengeluaran AS keterangan");
		$this->datatables->from('pengeluaran');
		$this->datatables->where('sekarang',$detail);
		return $this->datatables->generate();
	}
	

}