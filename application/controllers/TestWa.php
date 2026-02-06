<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestWa extends CI_Controller {

    public function index()
    {
        $this->load->library('Wa_gateway');

        // Hardcoded test number - Change this or pass via URL
        // Example usage: index.php/TestWa/send?phone=08123456789&message=Hello
        $phone = $this->input->get('phone'); 
        $message = $this->input->get('message');

        if (empty($phone)) {
            echo "Please provide a phone number via GET parameter 'phone'.<br>";
            echo "Example: " . base_url('TestWa?phone=628123456789&message=TestMessage');
            return;
        }

        if (empty($message)) {
            $message = "This is a test message from SIM Sekolah.";
        }

        echo "Sending message to: $phone <br>";
        echo "Message: $message <br>";

        $result = $this->wa_gateway->send($phone, $message);

        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }
}
