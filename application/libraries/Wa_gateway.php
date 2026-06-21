<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_gateway {

    protected $CI;
    protected $api_url = 'http://localhost:5001'; 

    public function __construct()
    {
        $this->CI =& get_instance();
        // Allow override from config if available
        if ($this->CI->config->item('wa_gateway_url')) {
             $this->api_url = $this->CI->config->item('wa_gateway_url');
        }
    }

    /**
     * Send Text Message
     */
    public function send($to, $message)
    {
        $endpoint = $this->api_url . '/message/send-text';
        
        $data = [
            'to' => $to,
            'text' => $message
        ];

        return $this->_call_api($endpoint, $data);
    }

    /**
     * Start Session / Get QR Code
     * Now primarily handled by Socket.io on the frontend.
     * Kept for controller compatibility.
     */
    public function connect()
    {
        return ['status' => 'PENDING', 'message' => 'Silakan lihat QR Code pada UI Socket.io'];
    }

    /**
     * Get QR Code specifically 
     */
    public function get_qr()
    {
        return ''; 
    }

    /**
     * Send Payment Confirmation Notification
     */
    public function send_payment_confirmation($id_siswa, $item_name, $nominal, $method = 'Loket Sekolah')
    {
        // Try to find by ID first
        $siswa = $this->CI->db->get_where('siswa', ['id' => $id_siswa])->row();
        
        // If not found, try finding by NIS (sometimes ID and NIS are mixed in params)
        if (!$siswa) {
            $siswa = $this->CI->db->get_where('siswa', ['nis' => $id_siswa])->row();
        }



        $phone = ($siswa) ? $siswa->telpon : '';
        $name = ($siswa) ? ((isset($siswa->name)) ? $siswa->name : $siswa->siswa) : 'N/A';

        // Fallback to Users table if phone is empty
        if (empty($phone) && $siswa) {
            $user = $this->CI->db->get_where('users', ['email' => $siswa->nis])->row();
            // In this app, users might not have phone column, but let's check
            // Most likely it's only in siswa table.
        }

        // Debug Log to file
        $log = date('Y-m-d H:i:s') . " | Attempting Send | ID/NIS: " . $id_siswa . " | Name: " . $name . " | Target Telp: " . $phone . "\n";
        
        if (!empty($phone)) {
            $msg = "Assalamu’alaikum warahmatullahi wabarakatuh.\n\n";
            $msg .= "Yth. Bapak/Ibu Orang Tua/Wali dari ananda *" . $name . "*,\n\n";
            $msg .= "Alhamdulillah, kami informasikan bahwa pembayaran tagihan berikut telah *BERHASIL* kami terima:\n\n";
            $msg .= "Item: *" . $item_name . "*\n";
            $msg .= "Nominal: *Rp " . number_format($nominal, 0, ',', '.') . "*\n";
            $msg .= "Metode: *" . $method . "*\n";
            $msg .= "Waktu: " . date('d-m-Y H:i') . " WIB\n\n";
            $msg .= "Terima kasih atas kerja samanya. Semoga menjadi berkah bagi ananda dan keluarga.\n\n";
            $msg .= "Wassalamu’alaikum warahmatullahi wabarakatuh.\n\n";
            $msg .= "_Pesan ini dikirim otomatis oleh Sistem Informasi Keuangan MI DAAR EL-MUFLIHIN_";

            $result = $this->send($phone, $msg);
            $log .= "API Response: " . json_encode($result) . "\n";
            file_put_contents('debug_wa.log', $log, FILE_APPEND);
            return $result;
        } else {
            $log .= "Result: FAILED (No Phone Number Found)\n";
            file_put_contents('debug_wa.log', $log, FILE_APPEND);
            return false;
        }
    }

    private function _call_api($url, $data)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['status' => false, 'message' => $err];
        }

        return json_decode($response, true);
    }
}
