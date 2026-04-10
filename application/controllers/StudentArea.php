<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentArea extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('M_Siswa');
		$this->load->model('M_General');
		// We can't call is_login() here IF is_login() redirects to StudentArea, 
		// because of infinite loop potential if logic is flawed.
		// But based on my plan: if role==3 && class!=StudentArea -> redirect StudentArea.
		// So if class==StudentArea, it continues.
		is_login();
	}

	public function index(){
		// Get logged in user
		$user_id = $this->session->userdata('id');
		$user_role = $this->session->userdata('role');
		$user_email = $this->db->get_where('users', ['id' => $user_id])->row()->email;

		// Assuming email is nis@student.sim
		// Extract NIS
		$parts = explode('@', $user_email);
		if(count($parts) > 1 && $parts[1] == 'student.sim'){
			$nis = $parts[0];
		} else {
			// Fallback or error
			$nis = ''; 
		}

		// Get Student Data
		$student = $this->db->get_where('siswa', ['nis' => $nis])->row();

		if(!$student){
			echo "Data Siswa tidak ditemukan untuk akun ini.";
			return;
		}

		$data['title'] = 'Area Siswa | SIM Sekolah';
		$data['student'] = $student;
		
		// Year Selection Logic
		$tahun_sekarang = date('Y');
		if (date('m') < 7) { $tahun_sekarang = $tahun_sekarang - 1; }
		$selected_tahun = $this->input->get('tahun') ? $this->input->get('tahun') : $tahun_sekarang;
		
		$data['selected_tahun'] = $selected_tahun;
		$tahun_list = [];
		// Show up to 2 years back from current academic year
		for($i = 0; $i <= 2; $i++){
			$tahun_list[] = $tahun_sekarang - $i;
		}
		$data['tahun_list'] = $tahun_list;

		// 1. Tagihan Bulanan (SPP)
		$spp_nominal = $this->db->get_where('pembayaran', ['id' => 1])->row()->nominal;
		$months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
		$year = $selected_tahun;
		$spp_list = [];
		foreach($months as $m){
			$label = $m . '-' . $year;
			$cek = $this->db->get_where('spp', ['id_siswa' => $student->id, 'bulan' => $label])->row();
			$spp_list[] = (object)[
				'jenis' => 'SPP',
				'label_bayar' => $label,
				'nama_tagihan' => "SPP $label",
				'nominal' => $spp_nominal,
				'status' => $cek ? 'Lunas' : 'Belum Lunas',
				'tanggal_bayar' => $cek ? date('d-m-Y', strtotime($cek->tanggal)) : '-',
				'tempat_bayar' => $cek ? ($cek->metode_pembayaran ? $cek->metode_pembayaran : 'Loket') : '-'
			];
		}
		$data['tagihan_bulanan'] = $spp_list;

		// 2. Tagihan Lainnya
		$tagihan_lainnya = [];

		// Ujian
		$ujian_nominal = $this->db->get_where('pembayaran', ['id' => 2])->row()->nominal;
		foreach(['Ganjil', 'Genap'] as $p){
			$label = $p . '-' . $selected_tahun;
			$cek = $this->db->get_where('ujian', ['id_siswa' => $student->id, 'periode' => $label])->row();
			$tagihan_lainnya[] = (object)[
				'jenis' => 'UJIAN',
				'label_bayar' => $label,
				'nama_tagihan' => "Uang Ujian $label",
				'nominal' => $ujian_nominal,
				'status' => $cek ? 'Lunas' : 'Belum Lunas',
				'tanggal_bayar' => $cek ? date('d-m-Y', strtotime($cek->tanggal)) : '-',
				'tempat_bayar' => $cek ? ($cek->metode_pembayaran ? $cek->metode_pembayaran : 'Loket') : '-'
			];
		}

		// Buku
		$buku_nominal = $this->db->get_where('pembayaran', ['id' => 3])->row()->nominal;
		$cek = $this->db->get_where('buku', ['id_siswa' => $student->id, 'tahun_ajaran' => $selected_tahun])->row();
		$tagihan_lainnya[] = (object)[
				'jenis' => 'BUKU',
				'label_bayar' => $selected_tahun,
				'nama_tagihan' => "Uang Buku Tahun $selected_tahun",
				'nominal' => $buku_nominal,
				'status' => $cek ? 'Lunas' : 'Belum Lunas',
				'tanggal_bayar' => $cek ? date('d-m-Y', strtotime($cek->tanggal)) : '-',
				'tempat_bayar' => $cek ? ($cek->metode_pembayaran ? $cek->metode_pembayaran : 'Loket') : '-'
		];

		// Baju
		$baju_nominal = $this->db->get_where('pembayaran', ['id' => 4])->row()->nominal;
		$cek = $this->db->get_where('baju', ['id_siswa' => $student->id])->row();
		$tagihan_lainnya[] = (object)[
				'jenis' => 'BAJU',
				'label_bayar' => 'Baju Seragam',
				'nama_tagihan' => "Uang Baju Seragam",
				'nominal' => $baju_nominal,
				'status' => $cek ? 'Lunas' : 'Belum Lunas',
				'tanggal_bayar' => $cek ? date('d-m-Y', strtotime($cek->tanggal)) : '-',
				'tempat_bayar' => $cek ? ($cek->metode_pembayaran ? $cek->metode_pembayaran : 'Loket') : '-'
		];

		// Pendaftaran telah dihilangkan dari dashboard siswa, pembayaran hanya di loket / admin.
		
		$data['tagihan_lainnya'] = $tagihan_lainnya;
		
		// For sidebar active state
		$this->parents = 'Tagihan'; 
		
		// Use Template
		$this->load->library('template');
		$this->load->config('midtrans');
		$data['midtrans_client_key'] = $this->config->item('midtrans_client_key');
		
		$this->template->views('v_student_area', $data);
	}

	public function get_token(){
		$this->load->library('MidtransGateway');
		$jenis = strtoupper($this->input->post('jenis') ?? 'SPP');
		$label_bayar = $this->input->post('label_bayar');
		$nominal = $this->input->post('nominal');
		$user_id = $this->session->userdata('id');
		
		// Get Student Data
		$user_email = $this->db->get_where('users', ['id' => $user_id])->row()->email;
		$parts = explode('@', $user_email);
		$nis = $parts[0];
		$student = $this->db->get_where('siswa', ['nis' => $nis])->row();

		if(!$student){
			echo json_encode(['error' => 'Siswa tidak ditemukan']);
			return;
		}

		// Create unique Order ID
		// Format: JENIS-[ID_SISWA]-[LABEL]-[TIMESTAMP]
		$clean_label = str_replace([' ', '/'], '_', $label_bayar); 
		$order_id = $jenis . '-' . $student->id . '-' . $clean_label . '-' . time();

		$params = [
			'transaction_details' => [
				'order_id' => $order_id,
				'gross_amount' => (int)$nominal,
			],
			'customer_details' => [
				'first_name' => $student->name,
				'email' => $user_email,
				'phone' => $student->telpon ? $student->telpon : '0800000000',
			],
			'item_details' => [[
				'id' => $jenis.'-'.$clean_label,
				'price' => (int)$nominal,
				'quantity' => 1,
				'name' => "$jenis $label_bayar"
			]]
		];

		$snapToken = $this->midtransgateway->getSnapToken($params);

		echo json_encode($snapToken);
	}

	public function finish_payment(){
		$order_id = $this->input->post('order_id');
		if(!$order_id){
			echo json_encode(['error' => 'Order ID is required']);
			return;
		}

		$this->load->library('MidtransGateway');
		$status = $this->midtransgateway->status($order_id);

		if(isset($status['transaction_status'])){
			$trans_status = $status['transaction_status'];
			$fraud_status = isset($status['fraud_status']) ? $status['fraud_status'] : '';
			$gross_amount = $status['gross_amount'];

			if ($trans_status == 'capture') {
				if ($fraud_status == 'challenge') {
					// Challenge
					echo json_encode(['status' => 'pending', 'message' => 'Payment challenged']);
				} else {
					$this->_payment_success($order_id, $gross_amount);
					echo json_encode(['status' => 'success', 'message' => 'Payment verified']);
				}
			} else if ($trans_status == 'settlement') {
				$this->_payment_success($order_id, $gross_amount);
				echo json_encode(['status' => 'success', 'message' => 'Payment verified']);
			} else if ($trans_status == 'pending') {
				echo json_encode(['status' => 'pending', 'message' => 'Payment pending']);
			} else if ($trans_status == 'deny') {
				echo json_encode(['status' => 'failed', 'message' => 'Payment denied']);
			} else if ($trans_status == 'expire') {
				echo json_encode(['status' => 'failed', 'message' => 'Payment expired']);
			} else if ($trans_status == 'cancel') {
				echo json_encode(['status' => 'failed', 'message' => 'Payment canceled']);
			} else {
				echo json_encode(['status' => 'unknown', 'message' => 'Unknown status']);
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
			
			// Simple verification
			// Ideally verify signature_key here
			
			$transaction = $notif->transaction_status;
			$type = $notif->payment_type;
			$order_id = $notif->order_id;
			$fraud = $notif->fraud_status;

			if ($transaction == 'capture') {
				if ($type == 'credit_card'){
					if($fraud == 'challenge'){
						// Challenge
					} else {
						$this->_payment_success($order_id, $notif->gross_amount);
					}
				}
			} else if ($transaction == 'settlement'){
				$this->_payment_success($order_id, $notif->gross_amount);
			} else if ($transaction == 'pending'){
				// Pending
			} else if ($transaction == 'deny') {
				// Deny
			} else if ($transaction == 'expire') {
				// Expire
			} else if ($transaction == 'cancel') {
				// Cancel
			}
		}
	}

	private function _payment_success($order_id, $gross_amount){
		// Parse Order ID: JENIS-[ID_SISWA]-[LABEL]-[TIMESTAMP]
		$parts = explode('-', $order_id);
		if(count($parts) >= 4){
			$jenis = strtoupper($parts[0]);
			$siswa_id = $parts[1];
			
			$timestamp = end($parts);
			$label_parts = array_slice($parts, 2, -1);
			$label_raw = implode('-', $label_parts);
			$label = str_replace('_', ' ', $label_raw);
			
			$time = date('Y-m-d');

			if ($jenis == 'SPP') {
				$cek = $this->db->get_where('spp', ['id_siswa' => $siswa_id, 'bulan' => $label])->num_rows();
				if($cek == 0){
					$data = ['id_siswa' => $siswa_id, 'time' => $time, 'bulan' => $label, 'nominal' => $gross_amount, 'metode_pembayaran' => 'Transfer Online (Midtrans)'];
					$this->db->insert('spp', $data);
					$this->M_General->cek_laporan();
					$this->M_General->update_kas('kas_masuk', $gross_amount);
				}
			} 
			else if ($jenis == 'UJIAN') {
				$cek = $this->db->get_where('ujian', ['id_siswa' => $siswa_id, 'periode' => $label])->num_rows();
				if($cek == 0){
					$data = ['id_siswa' => $siswa_id, 'time' => $time, 'periode' => $label, 'nominal' => $gross_amount, 'metode_pembayaran' => 'Transfer Online (Midtrans)'];
					$this->db->insert('ujian', $data);
					$this->M_General->cek_laporan();
					$this->M_General->update_kas('kas_masuk', $gross_amount);
				}
			}
			else if ($jenis == 'BUKU') {
				$cek = $this->db->get_where('buku', ['id_siswa' => $siswa_id, 'tahun_ajaran' => $label])->num_rows();
				if($cek == 0){
					$data = ['id_siswa' => $siswa_id, 'waktu' => $time, 'time' => $time, 'tahun_ajaran' => $label, 'nominal' => $gross_amount, 'metode_pembayaran' => 'Transfer Online (Midtrans)'];
					$this->db->insert('buku', $data);
					$this->M_General->cek_laporan();
					$this->M_General->update_kas('kas_masuk', $gross_amount);
				}
			}
			else if ($jenis == 'BAJU') {
				$cek = $this->db->get_where('baju', ['id_siswa' => $siswa_id])->num_rows();
				if($cek == 0){
					$data = ['id_siswa' => $siswa_id, 'waktu' => $time, 'time' => $time, 'nominal' => $gross_amount, 'metode_pembayaran' => 'Transfer Online (Midtrans)'];
					$this->db->insert('baju', $data);
					$this->M_General->cek_laporan();
					$this->M_General->update_kas('kas_masuk', $gross_amount);
				}
			}
			else if ($jenis == 'PENDAFTARAN') {
				// update siswa
				$this->db->where('id', $siswa_id);
				$this->db->update('siswa', ['bayar' => 1, 'metode_pembayaran' => 'Transfer Online (Midtrans)']);
				$this->db->insert('pendaftaran', ['siswa' => $siswa_id, 'time' => $time, 'nominal' => $gross_amount]);
				$this->M_General->cek_laporan();
				$this->M_General->update_kas('kas_masuk', $gross_amount);
			}
		}
	}
}
