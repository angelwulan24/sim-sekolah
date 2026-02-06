<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tanggal extends CI_Controller {

	private $parents = 'Tanggal';
	private $icon	 = 'fa fa-calendar';
	var $table 		 = 'tanggal';

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
        $this->load->library('Wa_gateway');
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb($this->parents.' Merah',$this->parents);

		$data['title']	= $this->parents.' Merah/Hari Libur | SIM Sekolah ';
		$data['judul']	= $this->parents.' Merah/Hari Libur';
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function getData (){
		header('Content-Type:application/json');
		echo $this->mod->getAllData();
	}

	function edit($id){
		header('Content-Type:application/json');
		$data = $this->M_General->getByID($this->table, 'id', $id, 'DESC')->row();
		echo json_encode($data);
	}

	function Simpan(){
        $insert = array(
                    'tgl'			=> filter_string($this->input->post('tanggal',TRUE)),
                    'keterangan'	=> filter_string($this->input->post('keterangan',TRUE))
                );

        $insert = $this->M_General->insert($this->table,$insert);
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function Ubah(){
		$data = array(
			'tgl'        => filter_string($this->input->post('tanggal', TRUE)),
			'keterangan' => filter_string($this->input->post('keterangan', TRUE))
		);

		$this->M_General->update(
			$this->table,
			$data,
			'id',
			$this->input->post('id')
		);

		echo json_encode(['status' => TRUE]);
	}	

	public function Hapus($id){
		$this->M_General->delete($this->table,'id',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

    public function KirimWa($id) {
        // Get Holiday Data
        $tgl_libur = $this->db->get_where('tanggal', ['id' => $id])->row_array();
        
        if (!$tgl_libur) {
            echo json_encode(['status' => false, 'message' => 'Data Tanggal tidak ditemukan']);
            return;
        }

        // Get Active Students with Phone Numbers
        $students = $this->db->select('name, telpon, wali')->from('siswa')
                             ->where("telpon != ''")
                             ->where("telpon IS NOT NULL")
                             ->where("status", "Aktif") // Assuming 'status' column exists for active students
                             ->get()->result_array();

        $count = 0;
        if (!empty($students)) {
            $formatted_date = date('d-m-Y', strtotime($tgl_libur['tgl']));
            $message_template = "Informasi Sekolah:\n\nKepada Yth. Wali Murid/Siswa *[NAMA]*,\n\nDiberitahukan bahwa pada terhitung tanggal *[TANGGAL]* adalah hari libur sekolah dengan keterangan: *[KETERANGAN]*.\n\nTerima kasih.\nAdmin Sekolah";

            foreach ($students as $student) {
                if (!empty($student['telpon'])) {
                    $message = str_replace(
                        ['[NAMA]', '[TANGGAL]', '[KETERANGAN]'], 
                        [$student['name'], $formatted_date, $tgl_libur['keterangan']], 
                        $message_template
                    );
                    
                    // Send WA
                    $this->wa_gateway->send($student['telpon'], $message);
                    $count++;
                    
                    // Verification/Delay could be added here to prevent rate limiting
                    // sleep(1); 
                }
            }
            
            echo json_encode(['status' => true, 'message' => "$count Pesan berhasil dikirim ke antrian."]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Tidak ada data siswa dengan nomor telepon.']);
        }
    }
}