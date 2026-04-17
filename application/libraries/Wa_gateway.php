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
