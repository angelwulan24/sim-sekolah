<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tagihan extends CI_Controller {

	private $parents = 'Tagihan';
	private $icon	 = 'fa fa-level-down';
	var $table 		 = 'tagihan';

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

		$data['title']	= 'Data '.$this->parents.' | SIM Sekolah ';
		$data['judul']	= 'Data '.$this->parents;
		$data['icon']	= $this->icon;

	    $this->template->views('Backend/'.$this->parents.'/v_'.$this->parents,$data);
	}

	public function getData()
	{
		$kelas = $this->input->post('is_kelas', true);
		$json  = $this->mod->getAllData($kelas);

		$this->output
			->set_content_type('application/json')
			->set_output($json);
	}

	public function Detail($id_siswa){
		$this->breadcrumb->append_crumb('SIM Sekolah ','Beranda');
		$this->breadcrumb->append_crumb($this->parents,base_url('Tagihan'));
		$this->breadcrumb->append_crumb('Detail Tagihan',$this->parents);

		$data['title']	= 'Detail Tagihan | SIM Sekolah ';
		$data['judul']	= 'Detail Tagihan';
		$data['icon']	= 'fa fa-search';

		$data['siswa'] = $this->db->query("SELECT s.*, k.nama as nama_kelas FROM siswa s LEFT JOIN kelas k ON s.kelas = k.id WHERE s.id = '$id_siswa'")->row();
        
		$tahun_filter = $this->input->get('tahun_ajaran', true);
		$data['tahun_filter'] = $tahun_filter;

        $data['list_tahun'] = $this->db->query("SELECT DISTINCT tahun_ajaran FROM tagihan WHERE id_siswa = '$id_siswa' AND tahun_ajaran IS NOT NULL AND tahun_ajaran != '' ORDER BY tahun_ajaran DESC")->result();

        $where_tahun = "";
        if (!empty($tahun_filter)) {
            $where_tahun = " AND tahun_ajaran = '$tahun_filter'";
        }

        // Ambil Tagihan SPP
        $data['tagihan_spp'] = $this->db->query("SELECT * FROM tagihan WHERE id_siswa = '$id_siswa' AND jenis_tagihan LIKE '%SPP%' $where_tahun 
            ORDER BY id ASC")->result();
        
        // Ambil Tagihan Lainnya (selain SPP)
        $data['tagihan_lainnya'] = $this->db->query("SELECT * FROM tagihan WHERE id_siswa = '$id_siswa' AND jenis_tagihan NOT LIKE '%SPP%' $where_tahun ORDER BY id ASC")->result();

        // Ambil list jenis transaksi (pembayaran), kecualikan yang mengandung kata SPP
        $data['jenis_transaksi'] = $this->db->query("SELECT id, nama FROM pembayaran WHERE nama NOT LIKE '%SPP%'")->result();

        $data['id_siswa'] = $id_siswa;

	    $this->template->views('Backend/'.$this->parents.'/v_Detail',$data);
	}

    public function Simpan_Manual(){
        $insert = array(
            'id_siswa'      => $this->input->post('id_siswa',TRUE),
            'jenis_tagihan' => filter_string(ucwords($this->input->post('jenis_tagihan'),TRUE)),
            'nominal'       => $this->input->post('nominal',TRUE),
            'tahun_ajaran'  => filter_string($this->input->post('tahun_ajaran',TRUE)),
            'status'        => 'Belum Lunas'
        );

        $this->M_General->insert($this->table,$insert);
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function Bayar($id_tagihan) {
        $update = array(
            'status' => 'Lunas',
            'waktu_bayar' => waktu()
        );
        $this->M_General->update($this->table, $update, 'id', $id_tagihan);
        
        // update kas
        $tagihan = $this->db->query("SELECT id_siswa, nominal, jenis_tagihan FROM tagihan WHERE id = '$id_tagihan'")->row();
        if ($tagihan) {
            $this->M_General->update_kas('kas_masuk', $tagihan->nominal);
            
            // Send WhatsApp Notification
            $this->load->library('Wa_gateway');
            $this->wa_gateway->send_payment_confirmation($tagihan->id_siswa, $tagihan->jenis_tagihan, $tagihan->nominal);
        }

        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function Bayar_Multi() {
        $ids = $this->input->post('ids');
        if (!empty($ids)) {
            $this->load->library('Wa_gateway');
            foreach ($ids as $id) {
                // cek jika belum lunas
                $ceklunas = $this->db->query("SELECT id_siswa, status, nominal, jenis_tagihan FROM tagihan WHERE id = '$id'")->row();
                if($ceklunas && $ceklunas->status == 'Belum Lunas') {
                    $update = array(
                        'status' => 'Lunas',
                        'waktu_bayar' => waktu()
                    );
                    $this->M_General->update($this->table, $update, 'id', $id);
                    $this->M_General->update_kas('kas_masuk', $ceklunas->nominal);

                    // Send WhatsApp Notification
                    $this->wa_gateway->send_payment_confirmation($ceklunas->id_siswa, $ceklunas->jenis_tagihan, $ceklunas->nominal);
                }
            }
            $data['status'] = TRUE;
            $data['ids'] = implode(',', $ids);
        } else {
            $data['status'] = FALSE;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function Cetak_Bukti($id) {
		$this->load->library('pdf');
		$this->load->helper('data');
		
        if (strpos($id, ',') !== false) {
            $tagihan = $this->db->query("SELECT t.*, s.name, s.nis FROM tagihan t JOIN siswa s ON t.id_siswa = s.id WHERE t.id IN ($id)")->result();
        } else {
            $tagihan = $this->db->query("SELECT t.*, s.name, s.nis FROM tagihan t JOIN siswa s ON t.id_siswa = s.id WHERE t.id = '$id'")->result();
        }

        if(count($tagihan) == 0) {
            show_error('Data pembayaran tidak ditemukan');
            return;
        }
        $t_first = $tagihan[0];

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
		$pdf->Cell(190, 7, 'BUKTI PEMBAYARAN TAGIHAN', 0, 1, 'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'Nama Siswa', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->SetFont('TIMES','B',11);
		$pdf->Cell(0, 6, $t_first->name, 0, 1);
		
		$pdf->SetFont('TIMES','',11);
		$pdf->Cell(35, 6, 'NIS', 0, 0);
		$pdf->Cell(5, 6, ':', 0, 0);
		$pdf->Cell(0, 6, $t_first->nis, 0, 1);

		$pdf->Ln(5);

        $pdf->SetFont('TIMES','B',10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(80, 8, 'Jenis Tagihan', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Waktu Bayar', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Nominal', 1, 1, 'C', true);

        $pdf->SetFont('TIMES','',10);
        $no = 1;
        $total = 0;
        foreach($tagihan as $t) {
            $pdf->Cell(10, 7, $no++, 1, 0, 'C');
            $pdf->Cell(80, 7, $t->jenis_tagihan, 1, 0, 'L');
            $pdf->Cell(50, 7, date('d-m-Y', strtotime($t->waktu_bayar)), 1, 0, 'C');
            $pdf->Cell(50, 7, 'Rp. '.number_format($t->nominal, 0, ',', '.'), 1, 1, 'R');
            $total += $t->nominal;
        }

		$pdf->SetFont('TIMES','B',10);
        $pdf->Cell(140, 8, 'Total Bayar', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Rp. '.number_format($total, 0, ',', '.'), 1, 1, 'R', true);
		
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
		
		$pdf->Output();
    }

    public function Hapus($id) {
        $this->M_General->delete($this->table, 'id', $id);
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function Hapus_Multi() {
        $ids = $this->input->post('ids');
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->M_General->delete($this->table, 'id', $id);
            }
            $data['status'] = TRUE;
        } else {
            $data['status'] = FALSE;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

}
