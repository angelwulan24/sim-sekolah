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
		$student = $this->db->query("SELECT s.*, s.nama_siswa AS name, s.nis AS nis, s.jk_siswa AS sex, s.telp_siswa AS telpon, s.tmp_lahir AS tempat, s.tgl_lahirsiswa AS tanggal, s.thn_ajaran AS tahun_ajaran, s.status_siswa AS status, k.nama_kelas AS kelas, s.foto_siswa AS foto, s.ortu_wali AS orangtua_wali FROM siswa s LEFT JOIN kelas k ON s.id_kelas = k.id_kelas WHERE s.nis = '$nis'")->row();

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
        $tahun_list = $this->db->query("SELECT DISTINCT j.tahun_ajaran FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan WHERE t.nis_siswa = '$student->nis' ORDER BY j.tahun_ajaran DESC")->result();
		$data['tahun_list'] = $tahun_list;

        $current_school_yr = current_school_year();

		// 1. Tagihan Bulanan (SPP) dari database
		$tagihan_db = $this->db->query("SELECT t.id_tagihan AS id, t.status, t.tgl_pembayaran AS waktu_bayar, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, j.tenggat_waktu, j.tahun_ajaran 
                                        FROM tagihan_siswa t
                                        JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan
                                        WHERE t.nis_siswa = '$student->nis' 
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
		$student = $this->db->get_where('siswa', ['nis' => $user->email])->row();

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
		$order_id = 'BLK-' . $student->nis . '-' . time();

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
		$this->load->helper('data');
		
		$tagihan = $this->db->query("
			SELECT t.id_tagihan AS id, t.tgl_pembayaran AS waktu_bayar, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, si.nama_siswa AS name, si.nis AS nis, k.nama_kelas, t.tgl_pembayaran
			FROM tagihan_siswa t
			JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan
			JOIN siswa si ON t.nis_siswa = si.nis 
			LEFT JOIN kelas k ON si.id_kelas = k.id_kelas
			WHERE t.id_tagihan = '$id'
		")->row();

		if(!$tagihan) {
			show_error('Data pembayaran tidak ditemukan');
			return;
		}

		$t_first = $tagihan;
		$tagihan_list = [$tagihan];

		$pdf = new FPDF('L', 'mm', 'A5');
        $pdf->SetMargins(10, 8, 10);
        $pdf->SetAutoPageBreak(false);
		$pdf->AddPage();
		
        // 1. Kop Surat (Header)
        $pdf->Image(FCPATH.'assets/dist/img/MI.png', 12, 8, 18, 18);
        
        $pdf->SetFont('TIMES', 'B', 10);
        $pdf->Cell(22);
        $pdf->Cell(0, 4.5, 'YAYASAN PENDIDIKAN ISLAM', 0, 1, 'L');
        
        $pdf->SetFont('TIMES', 'B', 13);
        $pdf->Cell(22);
        $pdf->Cell(0, 5.5, 'MADRASATUL QURAN DAAR EL-MUFLIHIN', 0, 1, 'L');
        
        $pdf->SetFont('TIMES', '', 8);
        $pdf->Cell(22);
        $pdf->Cell(0, 4, 'Perum Cikande Permai Blok G7/01 RT. 06/4 Kec. Cikande Kab. Serang', 0, 1, 'L');
        
        $pdf->Cell(22);
        $pdf->Cell(0, 4, 'Telp. 0823-1138-8825 - Email: midaarelmuflihin@gmail.com', 0, 1, 'L');
        
        // Double lines below header
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.6);
        $pdf->Line(10, 29, 200, 29);
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, 30, 200, 30);
        
        // 2. Title
        $pdf->Ln(4);
        $pdf->SetFont('TIMES', 'BU', 11);
        $pdf->Cell(0, 5, 'BUKTI PEMBAYARAN SISWA', 0, 1, 'C');
        $pdf->Ln(3);
        
        // 3. Info Siswa
        $pdf->SetFont('TIMES', '', 9.5);
        
        // Nama Siswa
        $pdf->Cell(22, 5.5, 'Nama Siswa', 0, 0);
        $pdf->Cell(3, 5.5, ':', 0, 0);
        $pdf->Cell(95, 5.5, $t_first->name, 0, 0);
        
        // Tanggal
        $pdf->Cell(15, 5.5, 'Tanggal', 0, 0);
        $pdf->Cell(3, 5.5, ':', 0, 0);
        $tgl_bayar = $t_first->tgl_pembayaran ? tanggal($t_first->tgl_pembayaran, 'bulan') : '-';
        $pdf->Cell(0, 5.5, $tgl_bayar, 0, 1);
        
        // Kelas
        $pdf->Cell(22, 5.5, 'Kelas', 0, 0);
        $pdf->Cell(3, 5.5, ':', 0, 0);
        $pdf->Cell(95, 5.5, $t_first->nama_kelas ? $t_first->nama_kelas : '-', 0, 1);
        $pdf->Ln(2.5);
        
        // 4. Tabel Transaksi
        $pdf->SetFont('TIMES', 'B', 9);
        // Table Header
        $pdf->Cell(10, 6, 'No.', 'TB', 0, 'C');
        $pdf->Cell(130, 6, 'Keterangan Pembayaran', 'TB', 0, 'L');
        $pdf->Cell(50, 6, 'Jumlah (Rp)', 'TB', 1, 'R');
        
        $pdf->SetFont('TIMES', '', 9);
        $no = 1;
        $total_bayar = 0;
        $max_rows = 5;
        $total_items = count($tagihan_list);
        $last_row_index = max($max_rows, $total_items);
        
        // Loop paid items
        foreach ($tagihan_list as $row) {
            $border = ($no == $last_row_index) ? 'B' : 0;
            $pdf->Cell(10, 5.5, $no . '.', $border, 0, 'C');
            $pdf->Cell(130, 5.5, $row->jenis_tagihan, $border, 0, 'L');
            $pdf->Cell(50, 5.5, number_format($row->nominal, 0, ',', '.'), $border, 1, 'R');
            $total_bayar += $row->nominal;
            $no++;
        }
        
        // Draw empty rows up to 5 rows if items are fewer, to match the example image
        for ($i = $no; $i <= $max_rows; $i++) {
            $border = ($i == $last_row_index) ? 'B' : 0;
            $pdf->Cell(10, 5.5, $i . '.', $border, 0, 'C');
            $pdf->Cell(130, 5.5, '', $border, 0, 'L');
            $pdf->Cell(50, 5.5, '', $border, 1, 'R');
        }
        
        // 5. Grand Total
        $pdf->Ln(1.5);
        $pdf->SetFont('TIMES', 'B', 9.5);
        $pdf->Cell(140, 6, 'Grand Total:', 0, 0, 'R');
        
        $pdf->Cell(50, 6, number_format($total_bayar, 0, ',', '.'), 0, 1, 'R');
        $pdf->Ln(2);
        
        // 6. Signatures (Tanda Tangan)
        $pdf->SetFont('TIMES', '', 9);
        $pdf->Cell(130);
        $pdf->Cell(60, 4, 'Cikande, ' . ($t_first->tgl_pembayaran ? tanggal($t_first->tgl_pembayaran, 'bulan') : tanggal(waktu(), 'bulan')), 0, 1, 'C');
        
        $pdf->Cell(60, 4.5, 'Mengetahui,', 0, 0, 'C');
        $pdf->Cell(70);
        $pdf->Cell(60, 4.5, 'Yang Menerima,', 0, 1, 'C');
        
        $pdf->Ln(12); // space for signature
        
        $pdf->SetFont('TIMES', 'B', 9);
        $pdf->Cell(60, 4.5, 'Kh.Satibi Salim, M.Pd.I', 0, 0, 'C');
        $pdf->Cell(70);
        $pdf->Cell(60, 4.5, 'Nani Nuraeni S.Pd', 0, 1, 'C');
        
        // 7. Catatan (Notes)
        $pdf->Ln(1);
        $pdf->SetFont('TIMES', 'I', 7.5);
        $pdf->Cell(0, 3.5, 'Catatan :', 0, 1);
        $pdf->Cell(0, 3.5, '- Disimpan sebagai bukti pembayaran yang SAH', 0, 1);
		
		$pdf->Output();
	}
}
