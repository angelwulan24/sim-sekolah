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

		$id_kelas = $this->input->get('id_kelas');
		$data['id_kelas'] = $id_kelas;
		$data['kelas'] = $this->db->query("SELECT id_kelas AS id, nama_kelas AS nama FROM kelas")->result();

		$this->db->select('siswa.nis_siswa AS id, siswa.nama_siswa AS name, siswa.telp_siswa AS telpon, kelas.nama_kelas');
		$this->db->from('siswa');
		$this->db->join('kelas', 'siswa.id_kelas = kelas.id_kelas', 'left');
		$this->db->where('siswa.status_siswa', 'Aktif');
		if(!empty($id_kelas)) {
			$this->db->where('siswa.id_kelas', $id_kelas);
		}
		$siswa = $this->db->get()->result();
		
		$year = date('Y');
		$month = date('n'); // Bulan ini (1-12)
        $semester = ($month >= 7) ? 'Ganjil' : 'Genap';
        $semester_label = $semester . '-' . $year;

        $months_array = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $current_year_setting = current_school_year();

		$tunggakan_list = [];

		foreach($siswa as $s) {
            $tunggakan_items = [];
            $total_tunggakan = 0;

            // Ambil semua tagihan yang belum lunas untuk siswa ini, saring SPP tahun ajaran lama
            $tagihan_db = $this->db->query("SELECT t.id_tagihan AS id, t.status, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, j.tenggat_waktu, j.tahun_ajaran 
                                            FROM tagihan_siswa t
                                            JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan
                                            WHERE t.nis_siswa = '$s->id' AND t.status = 'Belum Lunas'
                                              AND NOT (j.nama_tagihan LIKE '%SPP%' AND j.tahun_ajaran != '$current_year_setting')")->result();

            $current_month = (int)date('m');
            $current_year = (int)date('Y');
            $current_val = $current_year * 12 + $current_month;

            // var_dump();

            foreach($tagihan_db as $t) {
                $show = false;

                if (empty($t->tenggat_waktu)) {
                    $show = true; // No deadline = show immediately
                } else {
                    $t_year = (int)date('Y', strtotime($t->tenggat_waktu));
                    $t_month = (int)date('m', strtotime($t->tenggat_waktu));
                    $t_val = $t_year * 12 + $t_month;
                    if ($t_val <= $current_val) {
                        $show = true;
                    }
                }

                if ($show) {
                    $total_tunggakan += $t->nominal;
                    $tunggakan_items[] = $t;
                }
            }

            if($total_tunggakan > 0) {
                $s->total_tunggakan = $total_tunggakan;
                $s->rincian_tunggakan = $tunggakan_items;
                $tunggakan_list[] = $s;
            }
		}

        $data['tunggakan'] = $tunggakan_list;


		$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

    public function kirim_pengingat() {
        header('Content-Type: application/json');
        
        $id_siswa = $this->input->post('id_siswa');
        $rincian_raw = $this->input->post('rincian'); // Formatted string from JS (items separated by |)
        $total = $this->input->post('total');

        $siswa = $this->db->get_where('siswa', ['nis_siswa' => $id_siswa])->row();

        if ($siswa && !empty($siswa->telp_siswa)) {
            $phone = $siswa->telp_siswa;
            $msg = "Assalamu’alaikum warahmatullahi wabarakatuh.\n\n";
            $msg .= "Yth. Bapak/Ibu Orang Tua/Wali dari ananda *" . $siswa->nama_siswa . "*,\n\n";
            $msg .= "Dengan hormat, kami dari pihak sekolah bermaksud menyampaikan informasi terkait rincian tagihan administrasi yang telah mendekati jatuh tempo/masih tertunggak, berdasarkan data pada sistem kami sebagai berikut:\n\n";
            
            $items = explode('|', $rincian_raw);
            foreach($items as $item) {
                $msg .= $item . "\n";
            }

            $msg .= "\n*Total Tunggakan: Rp " . number_format($total, 0, ',', '.') . "*\n\n";
            $msg .= "Sehubungan dengan hal tersebut, kami memohon kesediaan Bapak/Ibu untuk segera melakukan pelunasan pembayaran melalui loket sekolah atau melalui aplikasi yang telah disediakan.\n\n";
            $msg .= "Demikian informasi ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.\n\n";
            $msg .= "Wassalamu’alaikum warahmatullahi wabarakatuh.";

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
    public function kirim_pengingat_masal() {
        header('Content-Type: application/json');
        
        $data_kirim = $this->input->post('data_kirim'); // Array of {id_siswa, rincian, total}
        
        if (empty($data_kirim) || !is_array($data_kirim)) {
            echo json_encode(['status' => false, 'message' => 'Tidak ada data yang dipilih']);
            return;
        }

        $berhasil = 0;
        $gagal = 0;

        foreach ($data_kirim as $data) {
            $id_siswa = $data['id_siswa'];
            $rincian_raw = $data['rincian'];
            $total = $data['total'];

            $siswa = $this->db->get_where('siswa', ['nis_siswa' => $id_siswa])->row();

            if ($siswa && !empty($siswa->telp_siswa)) {
                $phone = $siswa->telp_siswa;
                $msg = "Assalamu’alaikum warahmatullahi wabarakatuh.\n\n";
                $msg .= "Yth. Bapak/Ibu Orang Tua/Wali dari ananda *" . $siswa->nama_siswa . "*,\n\n";
                $msg .= "Dengan hormat, kami dari pihak sekolah bermaksud menyampaikan informasi terkait rincian tagihan administrasi yang telah mendekati jatuh tempo/masih tertunggak, berdasarkan data pada sistem kami sebagai berikut:\n\n";
                
                $items = explode('|', $rincian_raw);
                foreach($items as $item) {
                    $msg .= $item . "\n";
                }

                $msg .= "\n*Total Tunggakan: Rp " . number_format($total, 0, ',', '.') . "*\n\n";
                $msg .= "Sehubungan dengan hal tersebut, kami memohon kesediaan Bapak/Ibu untuk segera melakukan pelunasan pembayaran melalui loket sekolah atau melalui aplikasi yang telah disediakan.\n\n";
                $msg .= "Demikian informasi ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.\n\n";
                $msg .= "Wassalamu’alaikum warahmatullahi wabarakatuh.";

                try {
                    $result = $this->wa_gateway->send($phone, $msg);
                    if ($result) {
                        $berhasil++;
                    } else {
                        $gagal++;
                    }
                } catch (Exception $e) {
                    $gagal++;
                }
            } else {
                $gagal++;
            }
        }

        echo json_encode(['status' => true, 'message' => "Berhasil: $berhasil, Gagal: $gagal pesan pengingat."]);
    }
}
