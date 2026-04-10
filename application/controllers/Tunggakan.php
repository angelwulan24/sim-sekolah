<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tunggakan extends CI_Controller {

	private $parents = 'Tunggakan';
	private $icon	 = 'fa fa-exclamation-triangle';

	function __construct(){
		parent::__construct();
		is_login();
		get_breadcrumb();
		$this->load->library('Wa_gateway');
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb('Informasi Tunggakan',$this->parents);

		$data['title']	= 'Informasi Tunggakan | SIM Sekolah ';
		$data['judul']	= 'Informasi Tunggakan';
		$data['icon']	= $this->icon;

        // Mendapatkan referensi nominal (Default)
        $nom_spp_row = $this->db->get_where('pembayaran', ['nama' => 'Uang SPP'])->row();
        $nom_spp = $nom_spp_row ? $nom_spp_row->nominal : 70000;
        $spp_tenggat_val = $nom_spp_row ? $nom_spp_row->tenggat_waktu : null;
        $spp_tenggat = is_numeric($spp_tenggat_val) ? (int)$spp_tenggat_val : 0;

        $nom_ujian_row = $this->db->get_where('pembayaran', ['nama' => 'Uang Ujian'])->row();
        $nom_ujian = $nom_ujian_row ? $nom_ujian_row->nominal : 50000;

        $nom_buku_row = $this->db->get_where('pembayaran', ['nama' => 'Uang Buku'])->row();
        $nom_buku = $nom_buku_row ? $nom_buku_row->nominal : 5000;

        $nom_baju_row = $this->db->get_where('pembayaran', ['nama' => 'Uang Baju'])->row();
        $nom_baju = $nom_baju_row ? $nom_baju_row->nominal : 15000;

		$siswa = $this->db->get_where('siswa', ['status' => 'Aktif'])->result();
		
		$year = date('Y');
		$month = date('n'); // Bulan ini (1-12)
        $semester = ($month >= 7) ? 'Ganjil' : 'Genap';
        $semester_label = $semester . '-' . $year;

        $months_array = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $tahun_ajaran_sekarang = ($month >= 7) ? $year . '/' . ($year + 1) : ($year - 1) . '/' . $year;

		$tunggakan_list = [];

		foreach($siswa as $s) {
            $tunggakan_spp = 0;
            $tunggakan_ujian = 0;
            $tunggakan_buku = 0;
            $tunggakan_baju = 0;

            // Logika SPP: Harus bayar semua bulan dari Januari s.d. Bulan Ini di tahun yg berlaku
            $spp_paid = $this->db->get_where('spp', ['id_siswa' => $s->id])->result();
            $paid_spp_months = [];
            foreach($spp_paid as $sp) {
                // Periksa jika records spp ini berada dalam tahun $year
                if(strpos($sp->bulan, (string)$year) !== false) {
                    $paid_spp_months[] = explode('-', $sp->bulan)[0]; // Ambil nama bulannya (Misal "Januari")
                }
            }

            // Batas bulan penagihan SPP berdasarkan tenggat waktu
            $current_day = (int)date('j');
            $max_month_to_check = ($spp_tenggat > 0 && $current_day <= $spp_tenggat) ? ($month - 1) : $month;

            for($i = 0; $i < $max_month_to_check; $i++) {
                if(!in_array($months_array[$i], $paid_spp_months)) {
                    $tunggakan_spp += $nom_spp;
                }
            }

            // Logika Ujian: Cek bayaran untuk semester saat ini
            $ujian_paid = $this->db->get_where('ujian', ['id_siswa' => $s->id, 'periode' => $semester_label])->num_rows();
            if($ujian_paid == 0) {
                $tunggakan_ujian += $nom_ujian;
            }

            // Logika Uang Buku: Asumsikan dibayar satu kali per tahun ajaran
            $buku_paid = $this->db->get_where('buku', ['id_siswa' => $s->id, 'tahun_ajaran' => $tahun_ajaran_sekarang])->num_rows();
            if($buku_paid == 0) {
                $tunggakan_buku += $nom_buku;
            }

            // Logika Uang Baju: Asumsikan dibayar satu kali
            $baju_paid = $this->db->get_where('baju', ['id_siswa' => $s->id])->num_rows();
            if($baju_paid == 0) {
                $tunggakan_baju += $nom_baju;
            }

            $total_tunggakan = $tunggakan_spp + $tunggakan_ujian + $tunggakan_buku + $tunggakan_baju;

            if($total_tunggakan > 0) {
                $s->tunggakan_spp = $tunggakan_spp;
                $s->tunggakan_ujian = $tunggakan_ujian;
                $s->tunggakan_buku = $tunggakan_buku;
                $s->tunggakan_baju = $tunggakan_baju;
                $s->total_tunggakan = $total_tunggakan;
                $tunggakan_list[] = $s;
            }
		}

        $data['tunggakan'] = $tunggakan_list;

		$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

    public function kirim_pengingat() {
        header('Content-Type: application/json');
        
        $id_siswa = $this->input->post('id_siswa');
        $spp = $this->input->post('spp');
        $ujian = $this->input->post('ujian');
        $buku = $this->input->post('buku');
        $baju = $this->input->post('baju');
        $total = $this->input->post('total');

        $siswa = $this->db->get_where('siswa', ['id' => $id_siswa])->row();

        if ($siswa && !empty($siswa->telpon)) {
            $phone = $siswa->telpon;
            $msg = "Halo Bpk/Ibu Orangtua/Wali dari siswa *" . $siswa->name . "*.\n\n";
            $msg .= "Kami dari sekolah ingin menginformasikan rincian tagihan tunggakan pembayaran rutin (" . date('M Y').") yang direkap pada sistem kami yang belum diselesaikan:\n\n";
            if($spp > 0) $msg .= "- SPP Rutin: Rp " . number_format($spp, 0, ',', '.') . "\n";
            if($ujian > 0) $msg .= "- Ujian: Rp " . number_format($ujian, 0, ',', '.') . "\n";
            if($buku > 0) $msg .= "- Buku: Rp " . number_format($buku, 0, ',', '.') . "\n";
            if($baju > 0) $msg .= "- Baju: Rp " . number_format($baju, 0, ',', '.') . "\n";
            $msg .= "\n*Total Biaya Tunggakan: Rp " . number_format($total, 0, ',', '.') . "*\n\n";
            $msg .= "Mohon untuk segera diselesaikan. Terima kasih atas pengertian dan kerja samanya.";

            try {
                $result = $this->wa_gateway->send($phone, $msg);
                echo json_encode(['status' => true, 'message' => 'Pesan pengingat berhasil dikirim ke nomor whatsapp ' . $phone]);
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => 'Gagal mengirim pesan melalui Gateway: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'Siswa/Orangtua belum memiliki nomor telpon aktif pada sistem.']);
        }
    }
}
