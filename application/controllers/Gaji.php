<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gaji extends CI_Controller {

	private $parents = 'Gaji';
	private $icon	 = 'fa fa-calculator';
	var $table 		 = 'gaji';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
	}

	function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb($this->parents.' Guru',$this->parents);

		$data['title']	= 'Pembayaran '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pembayaran '.$this->parents;
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		echo $this->mod->getAllData();
	}

	function getGaji(){
		header('Content-Type:application/json');
		$n = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 6")->row_array();
		$nominal = isset($n['nominal']) ? $n['nominal'] : 0;
		echo json_encode($nominal);
	}


	function Detail($id){

		$this->load->helper('data');
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('SPP'));
		$this->breadcrumb->append_crumb('Detail Pembayaran SPP',$this->parents);

		$data['title']	= 'Detail Pembayaran '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Detail Pembayaran '.$this->parents;
		$data['icon']	= $this->icon;
		$data['isi']	= $this->M_General->getByID('gaji','id_guru',$id,'DESC')->result();

	$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);

	}

	function Bayar() {
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Gaji'));
		$this->breadcrumb->append_crumb('Bayar Gaji',$this->parents);

		$data['title']	= 'Form Pembayaran Gaji Guru | SIM Sekolah ';
		$data['judul']	= 'Form Pembayaran Gaji Guru';
		$data['icon']	= $this->icon;
		
		$data['guru']	= $this->db->query("SELECT * FROM guru ORDER BY status DESC, name ASC")->result();
		$query_paid = $this->db->query("SELECT DISTINCT periode FROM gaji")->result();
		$paid_months = array();
		foreach($query_paid as $pm) {
			$paid_months[] = $pm->periode;
		}
		$data['paid_months'] = $paid_months;
		$n = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 6")->row_array();
		$data['tarif_per_jam'] = isset($n['nominal']) ? $n['nominal'] : 0;

		$this->template->views('Backend/'.$this->parents.'/v_Bayar',$data);
	}

	function Simpan(){

		$bln = filter_string($this->input->post('bulan',TRUE));
        $id_guru_arr = $this->input->post('id_guru');
        $jam_arr = $this->input->post('jam');

        $n = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 6")->row_array();
        $tarif_per_jam = isset($n['nominal']) ? $n['nominal'] : 0;

        $tarif_input = $this->input->post('tarif');
        if(!empty($tarif_input) && $tarif_input != $tarif_per_jam) {
            $this->db->query("UPDATE pembayaran SET nominal = ".$this->db->escape($tarif_input)." WHERE id = 6");
            $tarif_per_jam = $tarif_input;
        }

        $total_kas = 0;
        $success = false;

        if(!empty($id_guru_arr)) {
            for($i=0; $i<count($id_guru_arr); $i++) {
                $id_gur = $id_guru_arr[$i];
                $jam = $jam_arr[$i];

                if(empty($jam) || $jam <= 0) continue;

                $cek = $this->db->query("SELECT id FROM gaji WHERE id_guru = '$id_gur' AND periode = '$bln' ")->num_rows();
                if ($cek > 0) continue;

                $total_gaji_guru = $jam * $tarif_per_jam;

                $insert = array(
                    'id_guru'	=> $id_gur,
                    'periode'	=> $bln,
                    'time'	   => waktu(),
                    'jam'		=> $jam,
                    'nominal'	=> $tarif_per_jam
                );
                
                $this->M_General->insert($this->table,$insert);
                $total_kas += $total_gaji_guru;
                $success = true;
            }
        }

        if($total_kas > 0) {
            $this->M_General->update_kas('kas_keluar',$total_kas);
        }
        
        if($success) {
            $data['status'] = TRUE;
        } else {
            $data['status'] = FALSE;
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

}