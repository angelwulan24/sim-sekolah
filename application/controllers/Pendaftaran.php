<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pendaftaran extends CI_Controller {

	private $parents = 'Pendaftaran';
	private $icon	 = 'fa fa-file-text-o';
	var $table 		 = 'temp';
	private $filename = "import_pendaftaran"; 

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		//$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
	}

	public function index(){

		$this->load->helper('data');

		// Clear old flashdata
		$this->session->flashdata('success');
		$this->session->flashdata('error');

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb($this->parents,$this->parents);

		$data['title']	= $this->parents.' | SIM Sekolah ';
		$data['judul']	= $this->parents;
		$data['icon']	= $this->icon;
		$data['isi']	= $this->db->query("SELECT temp.id, temp.name, temp.nis, temp.alamat, temp.sex, temp.orangtua_wali, temp.bayar, temp.tempat, temp.tanggal, temp.telpon, temp.kelas, kelas.nama as nama_kelas FROM temp LEFT JOIN kelas ON temp.kelas = kelas.id")->result();
		$data['bayar']	= $this->db->query("SELECT nominal FROM pembayaran WHERE id = 5 ")->row_array();

		$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	function import(){

		$upload = $this->M_General->upload_file($this->filename);
	
		if ($upload['status'] == true){  
		include APPPATH.'third_party/PHPExcel/PHPExcel.php';
		$excelreader = new PHPExcel_Reader_Excel2007();
		$loadexcel = $excelreader->load('excel/'.$this->filename.'.xlsx');
		$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);
		
		$data = array();
		$numrow = 1;
		foreach($sheet as $row){
			if($numrow > 1){
				// Kita push (add) array data ke variabel data
				array_push($data, array(
					'name'=>$row['A'],
					'nis'=>$row['B'],
					'tempat'=>$row['C'],
					'tanggal'=>$row['D'],
					'sex'=>$row['E'],
					'orangtua_wali'=>$row['F'],
					'alamat'=>$row['G'],
					'telpon'=>$row['H'],
					'kelas'=>$row['I'],
				));
			}
			
			$numrow++;
		}
		$this->db->insert_batch('temp',$data);
		 $this->M_General->delete('temp','tanggal','0000-00-00');
		
       $this->session->set_flashdata('success','Berhasil import Data Baru!');
    	}
    	else{
    		$this->session->set_flashdata('error','Gagal import Data Baru!');
    	}
     redirect($this->uri->segment(1),'refresh');
	}

	function Simpan(){
        $insert = array(
                    'name'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'sex'		=> $this->input->post('gender',TRUE),
                    'nis' 		=> $this->input->post('nis',TRUE),
                    'tempat'	=> filter_string($this->input->post('tempat',TRUE)),
                    'tanggal'	=> filter_string($this->input->post('tanggal',TRUE)),
                    'alamat'	=> filter_string($this->input->post('alamat',TRUE)),
                    'orangtua_wali'		=> filter_string($this->input->post('orangtua_wali',TRUE)),
                    'telpon'		=> filter_string($this->input->post('telpon',TRUE)),
                    'kelas'     => $this->input->post('kelas',TRUE)
                );

        $insert = $this->M_General->insert($this->table,$insert);
        $this->session->set_flashdata('success','Berhasil menambahkan Data Baru!');

        redirect($this->uri->segment(1),'refresh');
	}

	function Bayar(){
		$id = $this->input->post('id');
		$har = $this->input->post('harga');
		$nam = $this->input->post('nama');
		$this->db->where('id',$id);
		$this->db->update('temp',array('bayar'=>'1'));

		$this->db->insert('pendaftaran',array('siswa'=>$nam,'nominal'=>$har,'time'	    => waktu(),));
		$this->M_General->update_kas('kas_masuk',$har);

		// Send WhatsApp Notification
		$this->load->library('Wa_gateway');
		$this->wa_gateway->send_payment_confirmation($id, "Uang Pendaftaran", $har);

		 $this->session->set_flashdata('success','Pembayaran Uang Pendaftaran Berhasil!');

		redirect($this->uri->segment(1),'refresh');
	}

	function edit($id){
		header('Content-Type:application/json');
		$data = $this->M_General->getByID($this->table, 'id', $id, 'DESC')->row();
		echo json_encode($data);
	}

	function cetak_bukti($id){
		// Disable error reporting temporarily
		error_reporting(0);
		
		require_once APPPATH.'/third_party/fpdf/fpdf.php';
		
		// Get data pendaftaran
		$data = $this->db->get_where('temp', ['id' => $id])->row();
		$bayar = $this->db->query("SELECT nominal FROM pembayaran WHERE id = 5 ")->row_array();
		
		if(!$data) {
			show_error('Data pendaftaran tidak ditemukan');
			return;
		}

		$pdf = new FPDF('p','mm','A4');
        $pdf->SetMargins(10, 10, 10);
		$pdf->AddPage();
		
        // Header
        $pdf->Image(FCPATH.'assets/dist/img/MI.png', 10, 12, 28);
        $pdf->SetFont('TIMES','B',12);
        $pdf->Cell(28); 
        $pdf->Cell(162, 6, 'YAYASAN PENDIDIKAN ISLAM', 0, 1, 'C');
        $pdf->Cell(28);
        $pdf->SetFont('TIMES','B',16);
        $pdf->Cell(162, 8, 'MADRASATUL QURAN DAAR EL-MUFLIHIN', 0, 1, 'C');
        $pdf->Cell(28);
        $pdf->SetFont('TIMES','',10);
        $pdf->Cell(162, 5, 'Perum Cikande Permai Blok G7/01 RT. 06/4 Kec. Cikande Kab. Serang', 0, 1, 'C');
        $pdf->Cell(28);
        $pdf->Cell(162, 5, 'Telp.0823-1138-8825, email: midaarelmuflihin@gmail.com', 0, 1, 'C');
        
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.8);
        $pdf->Line(10, 42, 200, 42);
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, 43, 200, 43);
		
		$pdf->Ln(8);
		$pdf->SetFont('TIMES','B',12);
		$pdf->Cell(190, 7, 'BUKTI PEMBAYARAN PENDAFTARAN', 0, 1, 'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'Nama Siswa', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->SetFont('TIMES','B',11);
		$pdf->Cell(0, 6, $data->name, 0, 1);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'NIS', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->nis, 0, 1);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'Tempat, Tgl Lahir', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->tempat.', '.$data->tanggal, 0, 1);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'Orangtua / Wali', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $data->orangtua_wali, 0, 1);

		$pdf->Ln(5);
		$pdf->SetFont('TIMES','B',12);
		$pdf->Cell(35, 8, 'TOTAL BAYAR', 0, 0);
		$pdf->Cell(5, 8, ':', 0, 0);
		$pdf->Cell(0, 8, 'Rp. '.number_format($bayar['nominal'], 0, ',', '.'), 0, 1);
		
		$pdf->Ln(15);
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(130);
		$pdf->Cell(60, 5, 'Cikande Permai, '.date('d F Y'), 0, 1, 'C');
		$pdf->Cell(130);
		$pdf->Cell(60, 5, 'Bendahara Sekolah,', 0, 1, 'C');
		
		$pdf->Ln(20);
		$pdf->Cell(130);
        $pdf->SetFont('TIMES','B',11);
		$pdf->Cell(60, 5, 'Nani Nuraeni S.Pd', 0, 1, 'C');
			
		$pdf->Output('I','Bukti_Pembayaran_'.$data->nis.'.pdf');
	}

	function Kelas(){
		        $insert = array(
                    'name'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'sex'		=> $this->input->post('sex',TRUE),
                    'nis' 		=> $this->input->post('nis',TRUE),
                    'status' 	=> 'Aktif',
                    'kelas'		=> $this->input->post('kelas'),
                    'tempat'	=> filter_string($this->input->post('tempat',TRUE)),
                    'tanggal'	=> filter_string($this->input->post('tanggal',TRUE)),
                    'alamat'	=> filter_string($this->input->post('alamat',TRUE)),
                    'orangtua_wali'		=> filter_string($this->input->post('orangtua_wali',TRUE)),
                    'telpon'		=> filter_string($this->input->post('telpon',TRUE))
                );	
        $this->M_General->insert('siswa',$insert);
        $this->M_General->delete($this->table,'id',$this->input->post('id'));

        // Automatically Create Account with NIS as username
        $this->_create_account($insert['nis'], $insert['name'], $insert['tanggal']);

        $this->session->set_flashdata('success','Siswa Baru Berhasil ditambahkan!');

        redirect($this->uri->segment(1),'refresh');	
	}

	function Hapus(){
		$id = $this->input->post('id');

		$this->M_General->delete('temp','id',$id);
		$this->session->set_flashdata('success','Berhasil Menghapus Data ');
		redirect($this->uri->segment(1));
	}

	function Ubah(){
        $insert = array(
                    'name'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'sex'		=> $this->input->post('gender',TRUE),
                    'nis' 		=> $this->input->post('nis',TRUE),
                    'tempat'	=> filter_string($this->input->post('tempat',TRUE)),
                    'tanggal'	=> filter_string($this->input->post('tanggal',TRUE)),
                    'alamat'	=> filter_string($this->input->post('alamat',TRUE)),
                    'orangtua_wali'		=> filter_string($this->input->post('orangtua_wali',TRUE)),
                    'telpon'		=> filter_string($this->input->post('telpon',TRUE)),
                    'kelas'     => $this->input->post('kelas',TRUE)
                );
        $insert = $this->M_General->update($this->table,$insert,'id',$this->input->post('id'));
        $this->session->set_flashdata('success','Berhasil mengubah Data!');
        redirect($this->uri->segment(1),'refresh');
	}

    // Helper to create account
    private function _create_account($nis, $name, $dob){
        $username = $nis; 
        $cek = $this->db->get_where('users', ['email' => $username])->num_rows();

        // Format password from DOB (yyyy-mm-dd -> ddmmyy)
        $password_raw = date('dmy', strtotime($dob));

        if($cek == 0){
            $user = array(
                'name' 		=> $name,
                'email' 	=> $username, // Using email column for NIS as username
                'gambar'	=> 'user.png',
                'password'	=> password_hash($password_raw, PASSWORD_DEFAULT),
                'role'		=> 3, // Student
                'active'	=> '1'
            );
            $this->db->insert('users', $user);
        }
    }
}