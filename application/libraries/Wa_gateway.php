<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_gateway {

    protected $CI;
    // Default to localhost:5001 as per mimamch/wa-gateway docs
    protected $api_url = 'http://localhost:5001'; 
    protected $session = 'mysession'; // Default session name

    public function __construct()
    {
        $this->CI =& get_instance();
        // Allow override from config if available
        if ($this->CI->config->item('wa_gateway_url')) {
             $this->api_url = $this->CI->config->item('wa_gateway_url');
        }
        if ($this->CI->config->item('wa_gateway_session')) {
             $this->session = $this->CI->config->item('wa_gateway_session');
        }
    }

    /**
     * Send Text Message
     */
    public function send($to, $message)
    {
        $endpoint = $this->api_url . '/message/send-text';
        
        $data = [
            'session' => $this->session,
            'to' => $to,
            'text' => $message
        ];

        return $this->_call_api($endpoint, $data);
    }

    /**
     * Start Session / Get QR Code (or status)
     */
    public function connect()
    {
        // mimamch/wa-gateway: GET /session/start?session=NAME
        $endpoint = $this->api_url . '/session/start?session=' . $this->session;
        
        // This endpoint usually returns JSON. 
        // If the session is new, it might trigger the QR generation process on the server side.
        // Note: Some versions might return the QR encoded in JSON or ask to hit /session/qr.
        // We will assume it returns the status or data needed.
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Get QR Code specifically (if separated)
     * Some gateways use /session/qr?session=NAME
     */
    public function get_qr()
    {
        $endpoint = $this->api_url . '/session/qr/' . $this->session; // hypothetical endpoint based on common struct
        // Alternatively, the 'start' endpoint return might contain it.
        // For 'mimamch' specifically, often just hitting the root or /session/start creates it.
        return $endpoint; // Return URL to be loaded in img tag if it serves image directly
    }

    private function _call_api($url, $data)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); 
        // Using http_build_query for x-www-form-urlencoded or json_encode for application/json 
        // mimamch docs often use JSON body for POST, let's try JSON if form fails, but form is safer for simple PHP.
        // Actually, previous search said "json body: { ... }". So let's use JSON.

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
