<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Pengeluaran  extends CI_Model {

	function getAllData($filter = array()){
		$this->datatables->select("p.id_pengeluaran AS id, DATE_FORMAT(p.tgl_pengeluaran,'%d-%m-%Y') AS Tgl, p.ket_pengeluaran AS keterangan, p.nominal_pengeluaran AS Total, p.bukti, g.id_gaji");
		$this->datatables->from('pengeluaran p');
		$this->datatables->join('gaji g', 'p.id_pengeluaran = g.id_pengeluaran', 'left');
		$this->datatables->where('g.id_gaji IS NULL');

		if(!empty($filter['jenis']) && !empty($filter['tanggal'])){
            if($filter['jenis'] == 'hari'){
                $this->datatables->where('DATE(p.tgl_pengeluaran)', $filter['tanggal']);
            } elseif($filter['jenis'] == 'bulan'){
                $this->datatables->where("DATE_FORMAT(p.tgl_pengeluaran, '%Y-%m') = ", $filter['tanggal']);
            } elseif($filter['jenis'] == 'tahun'){
                $this->datatables->where("YEAR(p.tgl_pengeluaran)", $filter['tanggal']);
            }
		}

		return $this->datatables->generate();
	}

	function getDetailData($detail =''){
		$this->datatables->select("p.id_pengeluaran AS id, DATE_FORMAT(p.tgl_pengeluaran,'%d-%m-%Y') AS Tgl, p.nominal_pengeluaran AS nominal, p.ket_pengeluaran AS keterangan");
		$this->datatables->from('pengeluaran p');
		$this->datatables->join('gaji g', 'p.id_pengeluaran = g.id_pengeluaran', 'left');
		$this->datatables->where('g.id_gaji IS NULL');
		if (!empty($detail)) {
			$this->datatables->where('DATE(p.tgl_pengeluaran)', $detail);
		}
		return $this->datatables->generate();
	}
	

}