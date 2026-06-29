<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Pengeluaran  extends CI_Model {

	function getAllData($filter = array()){
		// Query langsung dari pengeluaran (gaji sudah terintegrasi via FK, tidak perlu UNION lagi)
		$this->datatables->select("id_pengeluaran AS id, DATE_FORMAT(tgl_pengeluaran,'%d-%m-%Y') AS Tgl, ket_pengeluaran AS keterangan, nominal_pengeluaran AS Total, bukti");
		$this->datatables->from('pengeluaran');

		if(!empty($filter['jenis']) && !empty($filter['tanggal'])){
            if($filter['jenis'] == 'hari'){
                $this->datatables->where('DATE(tgl_pengeluaran)', $filter['tanggal']);
            } elseif($filter['jenis'] == 'bulan'){
                $this->datatables->where("DATE_FORMAT(tgl_pengeluaran, '%Y-%m') = ", $filter['tanggal']);
            } elseif($filter['jenis'] == 'tahun'){
                $this->datatables->where("YEAR(tgl_pengeluaran)", $filter['tanggal']);
            }
		}

		return $this->datatables->generate();
	}

	function getDetailData($detail =''){
		// Filter by DATE(tgl_pengeluaran) instead of deprecated sekarang field
		$this->datatables->select("id_pengeluaran AS id, DATE_FORMAT(tgl_pengeluaran,'%d-%m-%Y') AS Tgl, nominal_pengeluaran AS nominal, ket_pengeluaran AS keterangan");
		$this->datatables->from('pengeluaran');
		if (!empty($detail)) {
			$this->datatables->where('DATE(tgl_pengeluaran)', $detail);
		}
		return $this->datatables->generate();
	}
	

}