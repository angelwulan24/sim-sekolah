<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp extends CI_Controller {

	private $parents = 'Whatsapp';
	private $icon	 = 'fa fa-whatsapp';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->library('Wa_gateway');
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb($this->parents,$this->parents);

		$data['title']	= $this->parents.' | SIM Sekolah ';
		$data['judul']	= $this->parents;
		$data['icon']	= $this->icon;

        // Check status (Simulation)
        // In a real scenario, we might hit the API to check connection status
        $data['status'] = 'Disconnected'; 

		$this->template->views('Backend/'.$this->parents.'/v_Connect',$data);
	}

    public function connect() {
        header('Content-Type: application/json');
        
        // Call the library to start session
        // Assuming the library is configured to hit localhost:5001
        try {
            $result = $this->wa_gateway->connect();
            
            // Check if result contains QR or status
            // This depends on the exact response of mimamch. 
            // If it returns { "status": "CREATED", "qr": "..." } or similar.
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
