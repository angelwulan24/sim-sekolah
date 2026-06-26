<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends CI_Controller {

	private $parents = 'Transaksi';
	private $icon	 = 'fa fa-money';
	var $table 		 = 'jenis_tagihan';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb('Jenis Tagihan',$this->parents);

		$data['title']	= 'Jenis Tagihan | SIM Sekolah ';
		$data['judul']	= 'Jenis Tagihan';
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		echo $this->mod->getAllData();
	}

	function buat_kode($tipe){

		$this->db->select('RIGHT(jenis_tagihan.kode_tagihan,4) as Kode',FALSE);
		$this->db->order_by('kode_tagihan','DESC');
		$this->db->limit(1);

		$q = $this->db->get('jenis_tagihan');

		if($q->num_rows() <> 0){

			$data = $q->row();

			$kode = intval($data->Kode) + 1;
		}
		else {

			$kode = 1;
		}

		$kodemax = str_pad($kode,4,"0",STR_PAD_LEFT);
		$kodejadi = $tipe."-".$kodemax;

		echo json_encode ($kodejadi);
	}


	public function edit($id){
		$data = $this->M_General->getByID($this->table,'kode_tagihan',$id,'kode_tagihan')->row();
		echo json_encode($data);
	}

	function Simpan(){
		$kelas_tagihan = $this->input->post('kelas',TRUE);
		$id_kelas = ($kelas_tagihan == 'Semua') ? NULL : $kelas_tagihan;
		$tahun_ajaran = filter_string($this->input->post('tahun_ajaran',TRUE));
		$nama_tagihan = filter_string($this->input->post('nama',TRUE));
		$nominal = filter_string($this->input->post('nominal',TRUE));
		$tenggat_waktu = filter_string($this->input->post('tenggat_waktu',TRUE));
		$kode_base = $this->input->post('kode',TRUE);

        // Fetch students
        if ($id_kelas === NULL) {
            $siswa = $this->db->query("SELECT nis_siswa FROM siswa")->result();
        } else {
            $siswa = $this->db->query("SELECT nis_siswa FROM siswa WHERE id_kelas = '$id_kelas'")->result();
        }

        $is_spp = (strpos(strtoupper($nama_tagihan), 'SPP') !== false);
        
        if ($is_spp) {
            // Generate monthly range
            $all_months = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
            
            $m1 = trim($this->input->post('bulan_awal', TRUE));
            $m2 = trim($this->input->post('bulan_akhir', TRUE));

            $start_idx = -1;
            $end_idx = -1;
            
            foreach($all_months as $idx => $m) {
                if (strcasecmp($m, $m1) == 0) $start_idx = $idx;
                if (strcasecmp($m, $m2) == 0) $end_idx = $idx;
            }

            $bulan = array();
            if ($start_idx != -1 && $end_idx != -1) {
                $curr = $start_idx;
                $count = 0;
                while($count < 12) {
                    $bulan[] = $all_months[$curr];
                    if ($curr == $end_idx) break;
                    $curr = ($curr + 1) % 12;
                    $count++;
                }
            } else {
                if ($m1 && $m2) {
                     $bulan = array($m1, $m2); 
                } else {
                     $bulan = array('Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni');
                }
            }
            
            // For each month, generate a separate jenis_tagihan and assign to students
            preg_match('/([A-Z]+)-([0-9]+)/', $kode_base, $matches);
            $prefix = isset($matches[1]) ? $matches[1] : 'KM';
            $start_num = isset($matches[2]) ? intval($matches[2]) : 1;

            foreach ($bulan as $bln) {
                $current_kode = $prefix . '-' . str_pad($start_num, 4, '0', STR_PAD_LEFT);
                $start_num++;

                // Map Indonesian month name to number (1-12)
                $m_idx = array_search($bln, $all_months);
                $m_num = ($m_idx !== false) ? ($m_idx + 1) : 1;

                // Determine correct year from tahun_ajaran (e.g., "2025/2026")
                $years = explode('/', $tahun_ajaran);
                if (count($years) == 2) {
                    $t_year = ($m_num >= 7) ? (int)$years[0] : (int)$years[1];
                } else {
                    $t_year = !empty($tahun_ajaran) ? (int)$tahun_ajaran : (int)date('Y');
                }

                // Format deadline date to the 14th day of the month and year
                $tenggat_spp = sprintf('%04d-%02d-14', $t_year, $m_num);

                // Insert fee type
                $fee_type = array(
                    'kode_tagihan'    => $current_kode,
                    'nama_tagihan'    => 'SPP - ' . $bln,
                    'nominal_tagihan' => $nominal,
                    'tenggat_waktu'   => $tenggat_spp,
                    'tahun_ajaran'    => $tahun_ajaran,
                    'id_kelas'        => $id_kelas
                );
                $this->db->insert('jenis_tagihan', $fee_type);

                // Assign to students
                if (!empty($siswa)) {
                    $data_tagihan = array();
                    foreach ($siswa as $s) {
                        $data_tagihan[] = array(
                            'nis_siswa'     => $s->nis_siswa,
                            'kode_tagihan'  => $current_kode,
                            'status'        => 'Belum Lunas',
                            'tgl_pembayaran'=> NULL
                        );
                    }
                    $chunks = array_chunk($data_tagihan, 100);
                    foreach ($chunks as $chunk) {
                        $this->db->insert_batch('tagihan_siswa', $chunk);
                    }
                }
            }
        } else {
            // Non-SPP single bill type
            $fee_type = array(
                'kode_tagihan'    => $kode_base,
                'nama_tagihan'    => ucwords($nama_tagihan),
                'nominal_tagihan' => $nominal,
                'tenggat_waktu'   => $tenggat_waktu,
                'tahun_ajaran'    => $tahun_ajaran,
                'id_kelas'        => $id_kelas
            );
            $this->db->insert('jenis_tagihan', $fee_type);

            if (!empty($siswa)) {
                $data_tagihan = array();
                foreach ($siswa as $s) {
                    $data_tagihan[] = array(
                        'nis_siswa'     => $s->nis_siswa,
                        'kode_tagihan'  => $kode_base,
                        'status'        => 'Belum Lunas',
                        'tgl_pembayaran'=> NULL
                    );
                }
                $chunks = array_chunk($data_tagihan, 100);
                foreach ($chunks as $chunk) {
                    $this->db->insert_batch('tagihan_siswa', $chunk);
                }
            }
        }

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Ubah(){
        $id = $this->input->post('id');
		$nom = filter_string($this->input->post('nominal',TRUE));
		$tenggat = filter_string($this->input->post('tenggat_waktu',TRUE));
		$tahun_ajaran = filter_string($this->input->post('tahun_ajaran',TRUE));
		$kelas_input = filter_string($this->input->post('kelas',TRUE));
		$id_kelas = ($kelas_input == 'Semua') ? NULL : $kelas_input;

        $update_data = array(
                             'nominal_tagihan'	=> $nom,
                             'tenggat_waktu' => $tenggat,
                             'tahun_ajaran' => $tahun_ajaran,
                             'id_kelas' => $id_kelas
                );
        $this->M_General->update($this->table,$update_data,'kode_tagihan',$id);

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
	
	function Hapus(){
        $this->M_General->delete($this->table,'kode_tagihan',$this->input->post('id'));
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
}