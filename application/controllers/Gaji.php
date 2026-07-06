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
		$nominal = $this->get_tarif_gaji();
		echo json_encode($nominal);
	}

	function Detail($id){

		$this->load->helper('data');
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Gaji'));
		$this->breadcrumb->append_crumb('Detail Pembayaran Gaji',$this->parents);

		$data['title']	= 'Detail Pembayaran '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Detail Pembayaran '.$this->parents;
		$data['icon']	= $this->icon;
		$data['id']     = $id;
		$data['guru']   = $this->db->get_where('guru', ['NUPTK' => $id])->row();
		$data['isi']	= $this->db->query("SELECT *, nominal_gaji AS nominal FROM gaji WHERE NUPTK = '$id' ORDER BY id_gaji DESC")->result();

	    $this->template->views('Backend/'.$this->parents.'/v_Detail',$data);

	}

	function Bayar() {
		$this->breadcrumb->append_crumb('SIM Sekolah ',base_url());
		$this->breadcrumb->append_crumb($this->parents,base_url('Gaji'));
		$this->breadcrumb->append_crumb('Bayar Gaji',$this->parents);

		$data['title']	= 'Form Pembayaran Gaji Guru | SIM Sekolah ';
		$data['judul']	= 'Form Pembayaran Gaji Guru';
		$data['icon']	= $this->icon;
		
		$data['guru']	= $this->db->query("SELECT NUPTK AS id, nama_guru AS name, NUPTK AS nip, jk_guru AS sex, bidang_studi AS bidang, status_guru AS status, foto_guru AS foto FROM guru ORDER BY status_guru DESC, nama_guru ASC")->result();
		$query_paid = $this->db->query("SELECT DISTINCT periode FROM gaji")->result();
		$paid_months = array();
		foreach($query_paid as $pm) {
			$paid_months[] = $pm->periode;
		}
		$data['paid_months'] = $paid_months;
		$data['tarif_per_jam'] = $this->get_tarif_gaji();

		$this->template->views('Backend/'.$this->parents.'/v_Bayar',$data);
	}

	function Simpan(){

		$bln = filter_string($this->input->post('bulan',TRUE));
        $id_guru_arr = $this->input->post('id_guru');
        $jam_arr = $this->input->post('jam');

        $tarif_per_jam = $this->get_tarif_gaji();

        $tarif_input = $this->input->post('tarif');
        if(!empty($tarif_input) && $tarif_input != $tarif_per_jam) {
            $this->update_tarif_gaji($tarif_input);
            $tarif_per_jam = $tarif_input;
        }

        $success = false;

        if(!empty($id_guru_arr)) {
            for($i=0; $i<count($id_guru_arr); $i++) {
                $id_gur = $id_guru_arr[$i];
                $jam = $jam_arr[$i];

                if(empty($jam) || $jam <= 0) continue;

                $cek = $this->db->query("SELECT id_gaji FROM gaji WHERE NUPTK = '$id_gur' AND periode = '$bln' ")->num_rows();
                if ($cek > 0) continue;

                $total_gaji_guru = $jam * $tarif_per_jam;

                // Get guru name for keterangan
                $guru = $this->db->get_where('guru', ['NUPTK' => $id_gur])->row();
                $nama_guru = $guru ? $guru->nama_guru : $id_gur;

                // Insert to pengeluaran FIRST (gaji berelasi ke pengeluaran)
                $pen_insert = array(
                    'nominal_pengeluaran' => $total_gaji_guru,
                    'tgl_pengeluaran'     => date('Y-m-d H:i:s'),
                    'ket_pengeluaran'     => 'Pembayaran Gaji: ' . $nama_guru . ' (' . $bln . ')'
                );
                $this->M_General->insert('pengeluaran', $pen_insert);
                $id_pengeluaran = $this->db->insert_id();

                // Insert gaji with FK to pengeluaran
                $insert = array(
                    'NUPTK'          => $id_gur,
                    'periode'        => $bln,
                    'jam'            => $jam,
                    'nominal_gaji'   => $tarif_per_jam,
                    'tgl_gaji'       => date('Y-m-d H:i:s'),
                    'id_pengeluaran' => $id_pengeluaran
                );
                $this->M_General->insert($this->table, $insert);
                $success = true;
            }
        }
        
        if($success) {
            $data['status'] = TRUE;
        } else {
            $data['status'] = FALSE;
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

    private function get_tarif_gaji() {
        $n = $this->db->get_where('jenis_tagihan', ['kode_tagihan' => 'GAJI'])->row_array();
        if (empty($n)) {
            $this->db->insert('jenis_tagihan', [
                'kode_tagihan' => 'GAJI',
                'nama_tagihan' => 'Tarif Gaji Guru',
                'nominal_tagihan' => '15000',
                'tenggat_waktu' => 'Setiap Bulan',
                'tahun_ajaran' => 'Global',
                'id_kelas' => NULL
            ]);
            return 15000;
        }
        return $n['nominal_tagihan'];
    }

    private function update_tarif_gaji($tarif) {
        $n = $this->db->get_where('jenis_tagihan', ['kode_tagihan' => 'GAJI'])->row_array();
        if (empty($n)) {
            $this->db->insert('jenis_tagihan', [
                'kode_tagihan' => 'GAJI',
                'nama_tagihan' => 'Tarif Gaji Guru',
                'nominal_tagihan' => $tarif,
                'tenggat_waktu' => 'Setiap Bulan',
                'tahun_ajaran' => 'Global',
                'id_kelas' => NULL
            ]);
        } else {
            $this->db->where('kode_tagihan', 'GAJI');
            $this->db->update('jenis_tagihan', ['nominal_tagihan' => $tarif]);
        }
    }

}