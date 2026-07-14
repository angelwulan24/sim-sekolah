<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Gaji extends CI_Model {

	function getAllData(){
		$this->datatables->select('NUPTK AS id, nama_guru AS name, jk_guru AS sex, NUPTK AS nip, telp_guru AS number');
		$this->datatables->from('guru');
		
		$this->datatables->add_column('view','<center><a href="javascript:void(0)" onclick="Detail(\'$1\')" class="btn btn-pastel-gaji btn-xs"><i class="fa fa-eye"></i> Detail</a></center> ','id');
		return $this->datatables->generate();
	}

	function getRiwayatData($filter = array())
	{
		$this->datatables->select("g.id_gaji AS id, DATE_FORMAT(g.tgl_gaji, '%d-%m-%Y %H:%i') as waktu_bayar, u.nama_guru as nama, u.NUPTK as nip, g.periode, g.jam, g.nominal_gaji, (g.jam * g.nominal_gaji) as total_gaji");
		$this->datatables->from("gaji g");
		$this->datatables->join("guru u", "g.NUPTK = u.NUPTK");

		if(!empty($filter['jenis']) && !empty($filter['tanggal'])){
            if($filter['jenis'] == 'hari'){
                $this->datatables->where('DATE(g.tgl_gaji)', $filter['tanggal']);
            } elseif($filter['jenis'] == 'bulan'){
                $this->datatables->where("DATE_FORMAT(g.tgl_gaji, '%Y-%m') = ", $filter['tanggal']);
            } elseif($filter['jenis'] == 'tahun'){
                $this->datatables->where("YEAR(g.tgl_gaji)", $filter['tanggal']);
            }
		}

		$btn = '<center>
			<span class="label label-success">Loket</span>
		</center>';

		$this->datatables->add_column('view', $btn, 'id');

		return $this->datatables->generate();
	}

}