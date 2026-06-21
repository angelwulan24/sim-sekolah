<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends CI_Controller {

	private $parents = 'Transaksi';
	private $icon	 = 'fa fa-money';
	var $table 		 = 'pembayaran';

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
		$kelas = $this->input->post('is_kelas');
		echo $this->mod->getAllData($kelas);
	}

	function buat_kode($tipe){

		$this->db->select('RIGHT(pembayaran.kode,4) as Kode',FALSE);
		$this->db->where('tipe',$tipe);
		$this->db->order_by('kode','DESC');
		$this->db->limit(1);

		$q = $this->db->get('pembayaran');

		if($q->num_rows() <> 0){

			$data = $q->row();

			$kode = intval($data->Kode) + 1;
		}
		else {

			$kode = 1;
		}

		$kodemax = str_pad($kode,4,"0",STR_PAD_LEFT);
		$kodejadi = $tipe."-".$kodemax;
		$cek = $this->db->last_query();

		echo json_encode ($kodejadi);
	}


	public function edit($id){
		$data = $this->M_General->getByID($this->table,'id',$id,'id')->row();
		echo json_encode($data);
	}

    function Simpan(){
		$kelas_tagihan = filter_string($this->input->post('kelas',TRUE));
		$tahun_ajaran = filter_string($this->input->post('tahun_ajaran',TRUE));
		$nama_tagihan = filter_string($this->input->post('nama',TRUE));
		$nominal = filter_string($this->input->post('nominal',TRUE));
		$tenggat_waktu = filter_string($this->input->post('tenggat_waktu',TRUE));
        
        $bulan_awal = $this->input->post('bulan_awal',TRUE);
        $bulan_akhir = $this->input->post('bulan_akhir',TRUE);
		
        $insert = array(
                    'kode'		=> $this->input->post('kode',TRUE),
                    'nama'		=> $nama_tagihan,
                    'nominal'	=> $nominal,
                    'tenggat_waktu'	=> $tenggat_waktu,
                    'tipe'		=> 'KM',
                    'tahun_ajaran' => $tahun_ajaran,
                    'kelas' => $kelas_tagihan
                );

        $this->M_General->insert($this->table,$insert);

        // Auto assign bills to students
        if ($kelas_tagihan == 'Semua Kelas') {
            $siswa = $this->db->query("SELECT id FROM siswa")->result();
        } else {
            // Join to match the text from select (e.g. "Kelas 1") to actual class name
            $siswa = $this->db->query("SELECT s.id FROM siswa s JOIN kelas k ON s.kelas = k.id WHERE k.nama = '$kelas_tagihan'")->result();
        }

        if(!empty($siswa)){
            $data_tagihan = array();
            $is_spp = (strpos(strtoupper($nama_tagihan), 'SPP') !== false);
            
            if ($is_spp) {
                // Generate range bulan
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
                    // Fallback jika pencarian gagal, gunakan input mentah jika ada
                    if ($m1 && $m2) {
                         $bulan = array($m1, $m2); 
                    } else {
                         $bulan = array('Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni');
                    }
                }
                
                foreach($siswa as $s) {
                    foreach ($bulan as $bln) {
                        $data_tagihan[] = array(
                            'id_siswa' => $s->id,
                            'jenis_tagihan' => 'SPP - ' . $bln,
                            'nominal' => $nominal,
                            'tahun_ajaran' => $tahun_ajaran,
                            'tenggat_waktu' => date('Y-m-d', strtotime(date('Y-m').'-10')), // Default tgl 10
                            'status' => 'Belum Lunas'
                        );
                    }
                }
            } else {
                foreach($siswa as $s) {
                    $data_tagihan[] = array(
                        'id_siswa' => $s->id,
                        'jenis_tagihan' => ucwords($nama_tagihan),
                        'nominal' => $nominal,
                        'tahun_ajaran' => $tahun_ajaran,
                        'tenggat_waktu' => $tenggat_waktu,
                        'status' => 'Belum Lunas'
                    );
                }
            }
            
            if (!empty($data_tagihan)) {
                // Gunakan chunk insert jika data sangat banyak untuk menghindari limit memory/SQL
                $chunks = array_chunk($data_tagihan, 100);
                foreach ($chunks as $chunk) {
                    $this->db->insert_batch('tagihan', $chunk);
                }
            }
        }

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Ubah(){
        $id = $this->input->post('id');
		$nom = filter_string($this->input->post('nominal',TRUE));
		$nam = filter_string($this->input->post('nama',TRUE));
		$tenggat = filter_string($this->input->post('tenggat_waktu',TRUE));
		$tahun_ajaran = filter_string($this->input->post('tahun_ajaran',TRUE));
		$kelas = filter_string($this->input->post('kelas',TRUE));

        // Ambil data lama untuk perbandingan
        $old = $this->db->get_where('pembayaran', ['id' => $id])->row();

        $update_data = array(
                             'nominal'	=> $nom,
                             'tenggat_waktu' => $tenggat,
                             'tahun_ajaran' => $tahun_ajaran,
                             'kelas' => $kelas
                );
        $this->M_General->update($this->table,$update_data,'id',$id);

        // Jika nominal berubah, update juga tagihan siswa yang belum lunas
        if ($old && $old->nominal != $nom) {
            $this->db->where('status', 'Belum Lunas');
            $this->db->where('tahun_ajaran', $old->tahun_ajaran);
            
            if (strpos(strtoupper($old->nama), 'SPP') !== false) {
                $this->db->like('jenis_tagihan', 'SPP - ', 'after');
            } else {
                $this->db->where('jenis_tagihan', $old->nama);
            }

            $this->db->update('tagihan', ['nominal' => $nom]);
        }

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
	function Hapus(){
        $this->M_General->delete($this->table,'id',$this->input->post('id'));
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
}