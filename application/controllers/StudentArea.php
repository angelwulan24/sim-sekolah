<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentArea extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('M_Siswa');
		$this->load->model('M_General');
		$this->load->library('Wa_gateway');
		is_login();
	}

	public function index(){
		// Get logged in user
		$user_id = $this->session->userdata('id');
		$user = $this->db->get_where('users', ['id_users' => $user_id])->row();
        
        if(!$user) {
            $this->session->sess_destroy();
            redirect('Auth');
        }

		// NIS is now stored directly in the 'email' column for students (role 3)
		$nis = $user->email;

		// Get Student Data with aliases to support the view keys
		$student = $this->db->query("SELECT s.*, s.nama_siswa AS name, s.nis_siswa AS nis, s.jk_siswa AS sex, s.telp_siswa AS telpon, s.tempat_lahirsiswa AS tempat, s.tgl_lahirsiswa AS tanggal, s.thn_ajaran AS tahun_ajaran, s.status_siswa AS status, k.nama_kelas AS kelas, s.foto_siswa AS foto, s.ortu_wali AS orangtua_wali FROM siswa s LEFT JOIN kelas k ON s.id_kelas = k.id_kelas WHERE s.nis_siswa = '$nis'")->row();

		if(!$student){
			echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
                    <img src='".base_url('assets/dist/img/MI.png')."' width='100'><br><br>
                    <h3>Data Siswa tidak ditemukan!</h3>
                    <p>Akun login Anda (NIS: $nis) tidak terhubung dengan biodata siswa di sistem.</p>
                    <p>Silakan hubungi admin sekolah untuk sinkronisasi data.</p>
                    <a href='".base_url('Auth/logout')."' style='color:blue;'>Kembali ke Login</a>
                  </div>";
			return;
		}

		$data['title'] = 'Area Siswa | SIM Sekolah';
		$data['student'] = $student;
		
		// Year Selection Logic
		// Default to student's current academic year if no year selected
		$selected_tahun = $this->input->get('tahun') ? $this->input->get('tahun') : $student->tahun_ajaran;
		
		$data['selected_tahun'] = $selected_tahun;
        
        // Generate academic years for filter (e.g. 2026/2027)
        $tahun_list = $this->db->query("SELECT DISTINCT j.tahun_ajaran FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan WHERE t.nis_siswa = '$student->nis_siswa' ORDER BY j.tahun_ajaran DESC")->result();
		$data['tahun_list'] = $tahun_list;

        $current_school_yr = current_school_year();

		// 1. Tagihan Bulanan (SPP) dari database
		$tagihan_db = $this->db->query("SELECT t.id_tagihan AS id, t.status, t.tgl_pembayaran AS waktu_bayar, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, j.tenggat_waktu, j.tahun_ajaran 
                                        FROM tagihan_siswa t
                                        JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan
                                        WHERE t.nis_siswa = '$student->nis_siswa' 
                                          AND j.tahun_ajaran = '$selected_tahun'
                                          AND NOT (t.status = 'Belum Lunas' AND j.nama_tagihan LIKE '%SPP%' AND j.tahun_ajaran != '$current_school_yr')
                                        ORDER BY t.id_tagihan ASC")->result();

		$spp_list = [];
		foreach($tagihan_db as $t){
            if (strpos(strtoupper($t->jenis_tagihan), 'SPP') !== false) {
                $month_label = str_replace('SPP - ', '', $t->jenis_tagihan);
                $is_lunas = ($t->status == 'Lunas');
                $tanggal_bayar = ($is_lunas && $t->waktu_bayar) ? date('d-m-Y', strtotime($t->waktu_bayar)) : '-';

                $spp_list[] = (object)[
                    'tagihan_id' => $t->id,
                    'spp_id' => null,
                    'jenis' => 'SPP',
                    'nominal' => $t->nominal,
                    'label_bayar' => $month_label,
                    'nama_tagihan' => $t->jenis_tagihan,
                    'status' => $is_lunas ? 'Lunas' : 'Belum Lunas',
                    'tanggal_bayar' => $tanggal_bayar,
                    'tempat_bayar' => $is_lunas ? 'Admin/Loket' : '-'
                ];
            }
		}
		$data['tagihan_bulanan'] = $spp_list;

        // 2. Tagihan Lainnya (Non-SPP dari database)
		$tagihan_lainnya = [];
        foreach($tagihan_db as $t){
            if (strpos(strtoupper($t->jenis_tagihan), 'SPP') === false) {
                // Simplified status check
                $tagihan_lainnya[] = (object)[
                    'tagihan_id' => $t->id,
                    'jenis' => explode(' ', $t->jenis_tagihan)[0],
                    'nama_tagihan' => $t->jenis_tagihan,
                    'nominal' => $t->nominal,
                    'label_bayar' => $t->tahun_ajaran,
                    'tenggat_waktu' => !empty($t->tenggat_waktu) ? date('d-m-Y', strtotime($t->tenggat_waktu)) : '-',
                    'status' => $t->status,
                    'tanggal_bayar' => ($t->status == 'Lunas') ? ($t->waktu_bayar ? date('d-m-Y', strtotime($t->waktu_bayar)) : '-') : '-',
                    'tempat_bayar' => ($t->status == 'Lunas') ? 'Admin/Loket' : '-'
                ];
            }
        }

		$data['tagihan_lainnya'] = $tagihan_lainnya;
		
		// For sidebar active state
		$this->parents = 'Tagihan'; 
		
		// Use Template
		$this->load->library('template');
		$this->load->config('midtrans');
		$data['midtrans_client_key'] = $this->config->item('midtrans_client_key');
		
		$this->template->views('v_student_area', $data);
	}

	public function get_token_bulk(){
		$this->load->library('MidtransGateway');
		$items_raw = $this->input->post('items');
		$items = json_decode($items_raw);
		
		if(empty($items)){
			echo json_encode(['error' => 'Tidak ada item terpilih']);
			return;
		}

		$user_id = $this->session->userdata('id');
		$user = $this->db->get_where('users', ['id_users' => $user_id])->row();
		$student = $this->db->get_where('siswa', ['nis_siswa' => $user->email])->row();

		$total_nominal = 0;
		$midtrans_items = [];
		$tagihan_ids = [];

		foreach($items as $it){
			$total_nominal += $it->nominal;
			$tagihan_ids[] = $it->tagihan_id;
			$midtrans_items[] = [
				'id' => $it->jenis . '-' . $it->tagihan_id,
				'price' => (int)$it->nominal,
				'quantity' => 1,
				'name' => $it->jenis . ' ' . $it->label_bayar
			];
		}

		// Order ID format: BLK-[STUDENT_NIS]-[TIMESTAMP]
		$order_id = 'BLK-' . $student->nis_siswa . '-' . time();

		$params = [
			'transaction_details' => [
				'order_id' => $order_id,
				'gross_amount' => (int)$total_nominal,
			],
			'customer_details' => [
				'first_name' => $student->nama_siswa,
				'email' => $user->email . '@mi-daarel-muflihin.sch.id',
				'phone' => $student->telp_siswa ? $student->telp_siswa : '0800000000',
			],
			'item_details' => $midtrans_items,
			'custom_field1' => implode(',', $tagihan_ids)
		];

		$snapToken = $this->midtransgateway->getSnapToken($params);
		echo json_encode($snapToken);
	}

	public function get_token(){
        $this->get_token_bulk();
	}

	public function finish_payment(){
		$order_id = $this->input->post('order_id');
		error_log($order_id);
		if(!$order_id){
			echo json_encode(['error' => 'Order ID is required']);
			return;
		}

		try {

			$this->load->library('MidtransGateway');
			$status = $this->midtransgateway->status($order_id);

		} catch (Exception $e) {
			error_log($e);
			echo json_encode(['error' => $e->getMessage()]);
			return;
		}

		if(isset($status['transaction_status'])){
			$trans_status = $status['transaction_status'];
			$fraud_status = isset($status['fraud_status']) ? $status['fraud_status'] : '';
			$gross_amount = $status['gross_amount'];

			if ($trans_status == 'capture' || $trans_status == 'settlement') {
				$this->_payment_success($order_id, $gross_amount);
				echo json_encode(['status' => 'success', 'message' => 'Payment verified']);
			} else {
				echo json_encode(['status' => 'pending', 'message' => 'Status: ' . $trans_status]);
			}
		} else {
			echo json_encode(['error' => 'Failed to check status']);
		}
	}

	public function notification(){
		$json_result = file_get_contents('php://input');
		$result = json_decode($json_result);

		if($result){
			$notif = $result;
			$transaction = $notif->transaction_status;
			$order_id = $notif->order_id;

			if ($transaction == 'capture' || $transaction == 'settlement') {
				$this->_payment_success($order_id, $notif->gross_amount);
			}
		}
	}

	private function _payment_success($order_id, $gross_amount){
		try {
			$this->load->library('MidtransGateway');
			$status = $this->midtransgateway->status($order_id);
			$time = date('Y-m-d H:i:s');

			// 1. Check custom_field1 (Our primary way for bulk payments)
			if(isset($status['custom_field1']) && !empty($status['custom_field1'])){
				$tagihan_ids = explode(',', $status['custom_field1']);
				foreach($tagihan_ids as $tagihan_id){
					$this->_process_single_tagihan($tagihan_id, $time);
				}
			} 
			// 2. Fallback to item_details (if provided)
			elseif(isset($status['item_details'])){
				foreach($status['item_details'] as $item){
					$parts = explode('-', $item['id']);
					if(count($parts) >= 2){
						$tagihan_id = $parts[1];
						$this->_process_single_tagihan($tagihan_id, $time);
					}
				}
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	private function _process_single_tagihan($tagihan_id, $time){
		$tagihan = $this->db->query("SELECT t.id_tagihan, t.nis_siswa, t.status, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan WHERE t.id_tagihan = '$tagihan_id'")->row();
		if($tagihan && $tagihan->status != 'Lunas'){
			// Update Tagihan
			$this->db->where('id_tagihan', $tagihan_id);
			$this->db->update('tagihan_siswa', ['status' => 'Lunas', 'tgl_pembayaran' => $time]);

			$this->M_General->update_kas('kas_masuk', $tagihan->nominal);

            // Send WhatsApp Notification (Triggered for both SPP and Other Bills)
            $this->wa_gateway->send_payment_confirmation($tagihan->nis_siswa, $tagihan->jenis_tagihan, $tagihan->nominal, 'Midtrans / Transfer Online');
		}
	}
	public function print_tagihan($id){
		$this->load->library('pdf');
		$tagihan = $this->db->query("
			SELECT t.id_tagihan AS id, t.tgl_pembayaran AS waktu_bayar, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, si.nama_siswa AS name, si.nis_siswa AS nis 
			FROM tagihan_siswa t
			JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan
			JOIN siswa si ON t.nis_siswa = si.nis_siswa 
			WHERE t.id_tagihan = '$id'
		")->row();

		if(!$tagihan) {
			show_error('Data pembayaran tidak ditemukan');
			return;
		}

		$pdf = new FPDF('P','mm','A4');
		$pdf->AddPage();
		
       // Header
       $pdf->Cell(3,5,'',0,1);
       $pdf->Image(FCPATH.'assets/dist/img/MI.png', 10, 10,33);
       $pdf->Cell(3,-5,'',0,1);
       $pdf->SetFont('TIMES','B',14);
       $pdf->Cell(189, 5, 'KEMENTRIAN AGAMA REPUBLIK INDONESIA', 0, 1, 'C');
       $pdf->Cell(189, 7, 'KANTOR KEMENTRIAN AGAMA KABUPATEN PATI', 0, 1, 'C');
       $pdf->SetFont('TIMES','B',16);
       $pdf->Cell(192, 7, 'MADRASAH ALIYAH NEGERI PATI', 0, 1, 'C');
       $pdf->SetFont('TIMES','B',12);
       $pdf->Cell(189, 5, 'YAYASAN PENDIDIKAN ISLAM', 0, 1, 'C');
       $pdf->SetFont('TIMES','B',16);
       $pdf->Cell(189, 7, 'MADRASATUL QURAN DAAR EL-MUFLIHIN', 0, 1, 'C');
       $pdf->SetFont('TIMES','',10);
       $pdf->Cell(189, 5, 'Perum Cikande Permai Blok G7/01 RT. 06/4 Kec. Cikande Kab. Serang', 0, 1, 'C');
       $pdf->Cell(189, 5, 'Telp.0823-1138-8825, email: midaarelmuflihin@gmail.com', 0, 1, 'C');
       $pdf->SetLineWidth(1);
       $pdf->Line(9, 46, 203, 46);
       $pdf->SetLineWidth(0);
       $pdf->Line(9, 47, 203, 47);
		
		$pdf->Cell(3,8,'',0,1);
		
		// Content
		$pdf->SetFont('TIMES','B',11);
		$pdf->Cell(0, 5, 'BUKTI PEMBAYARAN TAGIHAN', 0, 1, 'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('TIMES','',10);
		$pdf->Cell(40, 6, 'No. Transaksi', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, 'TAG-'.$tagihan->id, 0, 1);
		
		$pdf->Cell(40, 6, 'Tanggal Bayar', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $tagihan->waktu_bayar ? date('d-m-Y H:i', strtotime($tagihan->waktu_bayar)) : '-', 0, 1);
		
		$pdf->Cell(40, 6, 'Nama Siswa', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $tagihan->name, 0, 1);
		
		$pdf->Cell(40, 6, 'NIS', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $tagihan->nis, 0, 1);
		
		$pdf->Cell(40, 6, 'Jenis Tagihan', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $tagihan->jenis_tagihan, 0, 1);
		
		$pdf->Ln(5);
		$pdf->SetFont('TIMES','B',12);
		$pdf->Cell(40, 8, 'TOTAL BAYAR', 0, 0);
		$pdf->Cell(5, 8, ':', 0, 0);
		$pdf->Cell(0, 8, 'Rp. '.number_format($tagihan->nominal, 0, ',', '.'), 0, 1);
		
		// Footer / Signature
		$pdf->Ln(15);
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(130); // Spacer
		$pdf->Cell(60, 5, 'Cikande Permai, '.date('d F Y'), 0, 1, 'C');
		$pdf->Cell(130);
		$pdf->Cell(60, 5, 'Bendahara,', 0, 1, 'C');
		
		$pdf->Ln(20);
		$pdf->Cell(130);
		$pdf->SetFont('TIMES','B',11);
		$pdf->Cell(60, 5, 'Nani Nuraeni S.Pd', 0, 1, 'C');
		
		$pdf->Output();
	}
}
