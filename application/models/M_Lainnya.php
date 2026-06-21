<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Lainnya extends CI_Model {

	function getAllData(){
		$this->db->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
		$this->datatables->select("id_pemasukan AS id,sekarang,DATE_FORMAT(tgl_pemasukan,'%d-%m-%Y') AS Tgl, Sum(CAST(nominal_pemasukan AS DECIMAL(15,2))) AS Total, GROUP_CONCAT(ket_pemasukan SEPARATOR ', ') AS keterangan");
		$this->datatables->from('pemasukan');
		$this->datatables->group_by("tgl_pemasukan");
		return $this->datatables->generate();
	}

	function getDetailData($detail =''){
		$this->datatables->select("id_pemasukan AS id,DATE_FORMAT(tanggal,'%d-%m-%Y - %H:%i:%s WIB') AS Tgl,nominal_pemasukan AS nominal,ket_pemasukan AS keterangan");
		$this->datatables->from('pemasukan');
		$this->datatables->where('sekarang',$detail);
		return $this->datatables->generate();
	}
	

}