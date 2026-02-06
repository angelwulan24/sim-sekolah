<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buku extends CI_Controller {

	private $parents = 'Buku';
	private $icon	 = 'fa fa-money';
	var $table 		 = 'buku';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
		$this->load->helper('data');
        $this->load->library('Wa_gateway');
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb('Uang '.$this->parents,$this->parents);

		$data['title']	= 'Pembayaran Uang '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pembayaran Uang '.$this->parents;
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		$kls = $this->input->post('is_kelas');
		echo $this->M_General->getSiswa($kls); 
	}

	function getDetail($id){
		header('Content-Type:application/json');
		echo $this->mod->Detail($id);
	}

	function getBuku(){
		header('Content-Type:application/json');
		$n = $this->db->query("SELECT nominal FROM pembayaran WHERE nama = 'Uang Buku'")->row_array();
		echo json_encode($n['nominal']);
	}

	function UpdateData(){
		$id_sis = $this->input->post('id');
		$sis = get_siswa($id_sis);
		$tang = $this->input->post('tanggal');

		$quer = $this->db->query("SELECT id FROM buku WHERE id_siswa ='$id_sis' AND waktu ='$tang'")->num_rows();

		if ($quer > 0){
			$this->db->query("UPDATE buku SET nominal = '0' WHERE id_siswa='$id_sis' AND waktu = '$tang'");
			$n = $this->db->query("SELECT nominal FROM pembayaran WHERE nama = 'Uang Buku' ")->row_array();
			$this->M_General->update_kas('kas_keluar',$n['nominal']);
			$data['status'] = TRUE;
			    		$insert = array(
	                    'nominal'	=> $n['nominal'],
	                    'sekarang'	=> sekarang(),
	                    'time'	   => waktu(),
	                    'keterangan'	=>'Ubah Uang Buku dengan Nama '.$sis
	                );

	        $insert = $this->M_General->insert('lainnya',$insert);
		}
		else{
			$data['status'] = FALSE;	
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Detail($id){
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Buku'));
		$this->breadcrumb->append_crumb('Detail Pembayaran Uang Buku',$this->parents);

		$data['title']	= 'Pembayaran Uang '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Pembayaran Uang '.$this->parents;
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_Detail',$data);

	}

	function Simpan(){

		$tgl = filter_string($this->input->post('tanggal',TRUE));
		$harga = $this->input->post('harga');
		$hari = filter_string($this->input->post('total',TRUE));
		$id = $this->input->post('id_siswa');

		$data = array();

		$i = 0;
		$j = 0;
		do{

			$tgl2 = date('Y-m-d',strtotime('+'.$i.' days',strtotime($tgl)));
			$bln = $this->db->query("SELECT id FROM tanggal WHERE tgl = '$tgl2'")->num_rows();
			$udh = $this->db->query("SELECT id FROM buku WHERE waktu = '$tgl2' AND id_siswa = '$id' ")->num_rows();
			
			if ($udh == '0'){
				if ($bln == '0'){
					if (date("D",strtotime($tgl2)) != "Sun" && date("D",strtotime($tgl2)) != "Fri" && date("D",strtotime($tgl2)) != "Sat" ){
						array_push($data,array(
							'waktu'    => $tgl2,
							'nominal'  => $harga,
							'time'	   => waktu(),
							'id_siswa' => $id,
						));
						$j++;
					}
				}
			}
			$i++;

		}while($j<$hari);

		$total = $this->input->post('seluruh');
		$this->db->insert_batch('buku',$data);
		$this->M_General->update_kas('kas_masuk',$total);

        // Send WhatsApp Notification
        $siswa = $this->db->get_where('siswa', ['id' => $id])->row_array();
        if ($siswa && !empty($siswa['telpon'])) {
            $message = "Terima kasih, pembayaran Uang Buku atas nama *{$siswa['name']}* sebesar *" . rupiah($total) . "* untuk $hari hari telah berhasil diterima. \n\nTerima Kasih.";
            $this->wa_gateway->send($siswa['telpon'], $message);
        }
		
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

}