<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MidtransGateway {

    protected $CI;
    protected $serverKey;
    protected $isProduction;
    protected $apiUrl;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->config('midtrans');
        
        $this->serverKey = $this->CI->config->item('midtrans_server_key');
        $this->isProduction = $this->CI->config->item('midtrans_is_production');
        
        $this->apiUrl = $this->isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function getSnapToken($params)
    {
        $curl = curl_init();

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->serverKey . ':')
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return ['error' => $err];
        } else {
            return json_decode($response, true);
        }
    }
}
