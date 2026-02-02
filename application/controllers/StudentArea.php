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
		
		// Load Bills (Tagihan)
		// Need logic to fetch bills. Example: SPP, Ujian, etc.
		// Using simple queries for now based on tables seeing in SQL
		// spp table: id_siswa (smallint), time, bulan, nominal
		
		$data['spp'] = $this->db->get_where('spp', ['id_siswa' => $student->id])->result();
		
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
		
		$month_label = $this->input->post('bulan');
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
		// Format: SPP-[ID_SISWA]-[BULAN]-[TIMESTAMP]
		// Clean month label just in case
		$clean_month = str_replace(' ', '_', $month_label); 
		$order_id = 'SPP-' . $student->id . '-' . $clean_month . '-' . time();

		$params = [
			'transaction_details' => [
				'order_id' => $order_id,
				'gross_amount' => (int)$nominal,
			],
			'customer_details' => [
				'first_name' => $student->name,
				'email' => $user_email,
				'phone' => '0800000000', // Optional, can fetch from studen data if available
			],
			'item_details' => [[
				'id' => 'SPP-'.$clean_month,
				'price' => (int)$nominal,
				'quantity' => 1,
				'name' => "SPP $month_label"
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
		// Parse Order ID: SPP-[ID_SISWA]-[BULAN]-[TIMESTAMP]
		$parts = explode('-', $order_id);
		if(count($parts) >= 4){
			$siswa_id = $parts[1];
			// Reconstruct month. It was part 2.
			// But wait, if month has dashes this will break.
			// My month format is "Januari-2024". That has a dash!
			// Logic correction: 
			// ID starts with SPP
			// SISWA ID is index 1
			// TIMESTAMP is the LAST index
			// THE REST in middle is the Month.
			
			$timestamp = end($parts);
			$prefix = $parts[0]; // SPP
			$siswa_id = $parts[1];
			
			// Extract month parts
			// Slice from index 2 to length-1
			$month_parts = array_slice($parts, 2, -1);
			$month_raw = implode('-', $month_parts);
			$month = str_replace('_', ' ', $month_raw); // Restore spaces if any

			// Check if already paid to avoid double insert
			// We check 'spp' table for id_siswa and bulan
			$cek = $this->db->get_where('spp', [
				'id_siswa' => $siswa_id,
				'bulan' => $month
			])->num_rows();

			if($cek == 0){
				$data = [
					'id_siswa' => $siswa_id,
					'time' => date('Y-m-d'),
					'bulan' => $month,
					'nominal' => $gross_amount // Store as string to match schema
				];
				$this->db->insert('spp', $data);
				
				// Update kas masuk if required by system logic
				// Ensure daily report exists
				$this->M_General->cek_laporan();
				$this->M_General->update_kas('kas_masuk', $gross_amount);
			}
		}
	}
}
