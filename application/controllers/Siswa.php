<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller {

	private $parents = 'Siswa';
	private $icon	 = 'fa fa-users';
	var $table 		 = 'siswa';
	private $filename = "import_data"; 

	function __construct(){
		parent::__construct();

		is_login();
		get_breadcrumb();
		$this->load->model('M_'.$this->parents,'mod');
		$this->load->library('form_validation');
		$this->load->library('Datatables'); 
	}

	public function index(){

		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb($this->parents,$this->parents);

		$data['title']	= $this->parents.' | SIM Sekolah ';
		$data['judul']	= $this->parents;
		$data['icon']	= $this->icon;

	$this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	public function getData()
	{
		$kelas = $this->input->post('is_kelas', true); // XSS filter optional
		$json  = $this->mod->getAllData($kelas);

		$this->output
			->set_content_type('application/json')
			->set_output($json);
	}


	public function edit($id){
		$data = $this->M_General->getByID($this->table,'nis',$id,'DESC')->row();
		echo json_encode($data);
	}

	function import(){

		$upload = $this->M_General->upload_file($this->filename);
	
		if ($upload['status'] == true){  
			$sheet = $this->_read_excel('./excel/'.$this->filename.'.xlsx');
			
			$data = array();
			$numrow = 1;
			foreach($sheet as $row){
				if($numrow > 1){
					// Skip if both NIS and Nama are empty
					if (empty($row['B']) && empty($row['A'])) {
						continue;
					}

					// Parse dates
					$tgl_lahir = $this->_parse_date($row['F']);
					if (empty($tgl_lahir)) {
						$tgl_lahir = '2000-01-01'; // Safe default
					}
					
					$tgl_masuk = $this->_parse_date($row['J']);
					if (empty($tgl_masuk)) {
						$tgl_masuk = date('Y-m-d'); // Default to current date
					}

					// Create User Account with NIS as username
					$id_users = $this->_create_account($row['B'], $row['A'], $tgl_lahir);

					// Kita push (add) array data ke variabel data
					array_push($data, array(
						'nama_siswa'        => $row['A'],
						'nis'               => $row['B'],
						'jk_siswa'          => $row['C'],
						'agama_siswa'       => $row['D'],
						'tmp_lahir'         => $row['E'],
						'tgl_lahirsiswa'    => $tgl_lahir,
						'ortu_wali'         => $row['G'],
						'telp_siswa'        => $row['H'],
						'alamat_ssiwa'      => $row['I'],
						'tgl_masuk'         => $tgl_masuk,
						'thn_ajaran'        => $row['K'],
						'id_kelas'          => $row['L'],
						'status_siswa'      => $row['M'],
						'id_users'          => $id_users
					));
				}
				
				$numrow++;
			}
			if (!empty($data)) {
				$this->M_General->insert_multiple($data);

				// Assign existing bills to each imported student
				foreach ($data as $siswa_data) {
					if (isset($siswa_data['nis'])) {
						$this->_assign_existing_bills(
							$siswa_data['nis'],
							$siswa_data['id_kelas'],
							$siswa_data['thn_ajaran'],
							$siswa_data['tgl_masuk']
						);
					}
				}
			}
			
			$data['status'] = TRUE;
			$this->M_General->delete('siswa','id_kelas','0');
    	}
    	else{
    		$data['status'] = FALSE;
    	}
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Simpan(){
        $insert = array(
                    'nama_siswa'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'jk_siswa'		=> $this->input->post('gender',TRUE),
                    'nis' 	        => $this->input->post('nis',TRUE),
                    'id_kelas' 	    => $this->input->post('kelas',TRUE),
                    'tmp_lahir'	    => filter_string($this->input->post('tempat',TRUE)),
                    'tgl_lahirsiswa'	=> filter_string($this->input->post('tanggal',TRUE)),
                    'alamat_ssiwa'	=> filter_string($this->input->post('alamat',TRUE)),
                    'status_siswa'	=> filter_string($this->input->post('status',TRUE)),
                    'telp_siswa'    => filter_string($this->input->post('telpon',TRUE)),
                    'agama_siswa'     => filter_string($this->input->post('agama',TRUE)),
                    'ortu_wali'		=> filter_string($this->input->post('orangtua_wali',TRUE)),
                    'tgl_masuk'     => filter_string($this->input->post('tanggal_masuk',TRUE)),
                    'thn_ajaran'	=> filter_string($this->input->post('tahun_ajaran',TRUE))
                );

        if(!empty($_FILES['foto']['name'])){
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $config['upload_path']   = './assets/images/siswa/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']     = strtolower(str_replace(' ', '_', $insert['nama_siswa'])) . '_' . time() . '.' . $ext;
            $this->load->library('upload');
            $this->upload->initialize($config);
            if($this->upload->do_upload('foto')){
                $uploadData = $this->upload->data();
                $insert['foto_siswa'] = $uploadData['file_name'];
            }
        }

        // Create User Account with NIS as username
        $id_users = $this->_create_account($insert['nis'], $insert['nama_siswa'], $insert['tgl_lahirsiswa']);
        $insert['id_users'] = $id_users;

        $this->M_General->insert($this->table,$insert);
        $this->_assign_existing_bills($insert['nis'], $insert['id_kelas'], $insert['thn_ajaran'], $insert['tgl_masuk']);

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Ubah(){
        $id = $this->input->post('id');
        $old_data = $this->M_General->getByID($this->table,'nis',$id,'DESC')->row();
        
        $insert = array(
                    'nama_siswa'  	=> filter_string(ucwords($this->input->post('nama'),TRUE)),
                    'jk_siswa'		=> $this->input->post('gender',TRUE),
                    'nis' 	        => $this->input->post('nis',TRUE),
                    'id_kelas' 	    => $this->input->post('kelas',TRUE),
                    'tmp_lahir'	    => filter_string($this->input->post('tempat',TRUE)),
                    'tgl_lahirsiswa'	=> filter_string($this->input->post('tanggal',TRUE)),
                    'alamat_ssiwa'	=> filter_string($this->input->post('alamat',TRUE)),
                    'status_siswa'	=> filter_string($this->input->post('status',TRUE)),
                    'telp_siswa'    => filter_string($this->input->post('telpon',TRUE)),
                    'agama_siswa'     => filter_string($this->input->post('agama',TRUE)),
                    'ortu_wali'		=> filter_string($this->input->post('orangtua_wali',TRUE)),
                    'tgl_masuk'     => filter_string($this->input->post('tanggal_masuk',TRUE)),
                    'thn_ajaran'	=> filter_string($this->input->post('tahun_ajaran',TRUE))
                );

        if(!empty($_FILES['foto']['name'])){
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $config['upload_path']   = './assets/images/siswa/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['file_name']     = strtolower(str_replace(' ', '_', $insert['nama_siswa'])) . '_' . time() . '.' . $ext;
            $this->load->library('upload');
            $this->upload->initialize($config);
            if($this->upload->do_upload('foto')){
                $uploadData = $this->upload->data();
                $insert['foto_siswa'] = $uploadData['file_name'];
            }
        }
        $this->M_General->update($this->table,$insert,'nis',$id);

        // Update User Account associated with this student
        if($old_data && !empty($old_data->id_users)){
            $this->db->where('id_users', $old_data->id_users);
            $this->db->update('users', array('email' => $insert['nis'], 'nama_users' => $insert['nama_users'] ?? $insert['nama_siswa']));
        }

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function Hapus($id){
        $data_siswa = $this->M_General->getByID($this->table,'nis',$id,'DESC')->row();
        if($data_siswa){
            // Delete User Account using foreign key id_users
            if (!empty($data_siswa->id_users)) {
                $this->db->where('id_users', $data_siswa->id_users);
                $this->db->delete('users');
            }
            
            // Delete photo file if exists
            if (!empty($data_siswa->foto_siswa) && file_exists('./assets/images/siswa/' . $data_siswa->foto_siswa)) {
                unlink('./assets/images/siswa/' . $data_siswa->foto_siswa);
            }
        }

		$this->M_General->delete($this->table,'nis',$id);
		$data['status'] = TRUE;
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

    // Helper to create account
    private function _create_account($nis, $name, $dob){
        $username = $nis; 
        $user = $this->db->get_where('users', ['email' => $username])->row_array();
        
        // Format password from DOB (yyyy-mm-dd -> ddmmyy)
        $password_raw = date('dmy', strtotime($dob));

        if(!$user){
            $user_data = array(
                'nama_users' => $name,
                'email' 	 => $username,
                'gambar'	 => 'user.png',
                'password'	 => password_hash($password_raw, PASSWORD_DEFAULT),
                'role'		 => 3
            );
            $this->db->insert('users', $user_data);
            return $this->db->insert_id();
        } else {
            return $user['id_users'];
        }
    }

    private function _assign_existing_bills($nis_siswa, $id_kelas, $thn_ajaran, $tgl_masuk) {
        if (empty($thn_ajaran)) {
            return;
        }

        // Query all jenis_tagihan matching the academic year and class (or all classes)
        $this->db->group_start();
        $this->db->where('id_kelas', NULL);
        if (!empty($id_kelas)) {
            $this->db->or_where('id_kelas', $id_kelas);
        }
        $this->db->group_end();
        $this->db->where('tahun_ajaran', $thn_ajaran);
        $jenis_tagihan = $this->db->get('jenis_tagihan')->result();

        if (empty($jenis_tagihan)) {
            return;
        }

        $data_tagihan = array();
        foreach ($jenis_tagihan as $tag) {
            $is_spp = (strpos(strtoupper($tag->nama_tagihan), 'SPP') !== false);
            if ($is_spp) {
                // If student's entry date or tag deadline is empty, default to assigning it
                if (!empty($tgl_masuk) && !empty($tag->tenggat_waktu)) {
                    $entry_date = new DateTimeImmutable($tgl_masuk);
                    $tag_date = new DateTimeImmutable($tag->tenggat_waktu);

                    if ($tag_date < $entry_date) {
                        // Skip SPP for months that have already passed
                        continue;
                    }
                }
            }

            // Check if this student already has this bill assigned (to avoid duplicate assignment)
            $check = $this->db->get_where('tagihan_siswa', array(
                'nis_siswa' => $nis_siswa,
                'kode_tagihan' => $tag->kode_tagihan
            ))->row();

            if (!$check) {
                $data_tagihan[] = array(
                    'nis_siswa'      => $nis_siswa,
                    'kode_tagihan'   => $tag->kode_tagihan,
                    'status'         => 'Belum Lunas',
                    'tgl_pembayaran' => NULL
                );
            }
        }

        if (!empty($data_tagihan)) {
            $chunks = array_chunk($data_tagihan, 100);
            foreach ($chunks as $chunk) {
                $this->db->insert_batch('tagihan_siswa', $chunk);
            }
        }
    }

    // Native ZIP/XML Excel reader compatible with PHP 8.0+
    private function _read_excel($filepath) {
        $zip = new ZipArchive();
        if ($zip->open($filepath) !== TRUE) {
            return array();
        }
        
        $sharedStrings = array();
        $sharedStringsXML = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXML !== false) {
            $xml = simplexml_load_string($sharedStringsXML);
            foreach ($xml->si as $val) {
                if (isset($val->t)) {
                    $sharedStrings[] = (string)$val->t;
                } else if (isset($val->r)) {
                    $text = '';
                    foreach ($val->r as $r) {
                        $text .= (string)$r->t;
                    }
                    $sharedStrings[] = $text;
                } else {
                    $sharedStrings[] = '';
                }
            }
        }
        
        $sheetXML = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXML) {
            $zip->close();
            return array();
        }
        
        $xml = simplexml_load_string($sheetXML);
        $rows = array();
        foreach ($xml->sheetData->row as $row) {
            $rowIndex = (int)$row['r'];
            $rowData = array();
            foreach ($row->c as $cell) {
                $cellRef = (string)$cell['r'];
                preg_match('/^[A-Z]+/', $cellRef, $matches);
                $colLetter = $matches[0];
                
                $type = (string)$cell['t'];
                $val = (string)$cell->v;
                if ($type === 's') {
                    $rowData[$colLetter] = isset($sharedStrings[(int)$val]) ? $sharedStrings[(int)$val] : '';
                } else {
                    $rowData[$colLetter] = $val;
                }
            }
            // Ensure all columns from A to M are initialized
            foreach (range('A', 'M') as $col) {
                if (!isset($rowData[$col])) {
                    $rowData[$col] = '';
                }
            }
            $rows[$rowIndex] = $rowData;
        }
        $zip->close();
        return $rows;
    }

    // Parse different Excel date types and formats into YYYY-MM-DD
    private function _parse_date($value) {
        $value = trim($value);
        if (empty($value)) {
            return null;
        }
        
        // 1. If numeric (Excel serial date number)
        if (is_numeric($value)) {
            $unixTimestamp = ($value - 25569) * 86400;
            return date('Y-m-d', $unixTimestamp);
        }
        
        // 2. Try parsing DD-MM-YYYY string format
        $date = DateTime::createFromFormat('d-m-Y', $value);
        if ($date !== false) {
            return $date->format('Y-m-d');
        }
        
        // 3. Try parsing YYYY-MM-DD string format (fallback)
        $dateFallback = DateTime::createFromFormat('Y-m-d', $value);
        if ($dateFallback !== false) {
            return $dateFallback->format('Y-m-d');
        }
        
        // 4. Default raw conversion if strtotime works
        $time = strtotime($value);
        if ($time !== false) {
            return date('Y-m-d', $time);
        }
        
        return null;
    }
}