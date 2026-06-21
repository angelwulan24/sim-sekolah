<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tagihan extends CI_Controller {

	private $parents = 'Tagihan';
	private $icon	 = 'fa fa-level-down';
	var $table 		 = 'tagihan_siswa';

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

		$data['siswa'] = $this->db->query("SELECT s.nis_siswa AS nis, s.nama_siswa AS name, s.jk_siswa AS sex, s.telp_siswa AS telpon, s.foto_siswa AS foto, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.id_kelas = k.id_kelas WHERE s.nis_siswa = '$id_siswa'")->row();
        
		$tahun_filter = $this->input->get('tahun_ajaran', true);
		$data['tahun_filter'] = $tahun_filter;

        $data['list_tahun'] = $this->db->query("SELECT DISTINCT j.tahun_ajaran FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan WHERE t.nis_siswa = '$id_siswa' AND j.tahun_ajaran IS NOT NULL AND j.tahun_ajaran != '' ORDER BY j.tahun_ajaran DESC")->result();

        $where_tahun = "";
        if (!empty($tahun_filter)) {
            $where_tahun = " AND j.tahun_ajaran = '$tahun_filter'";
        }

        // Ambil Tagihan SPP
        $data['tagihan_spp'] = $this->db->query("SELECT t.id_tagihan AS id, t.status, t.tgl_pembayaran AS waktu_bayar, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, j.tahun_ajaran, j.tenggat_waktu 
            FROM tagihan_siswa t 
            JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan 
            WHERE t.nis_siswa = '$id_siswa' AND j.nama_tagihan LIKE '%SPP%' $where_tahun 
            ORDER BY t.id_tagihan ASC")->result();
        
        // Ambil Tagihan Lainnya (selain SPP)
        $data['tagihan_lainnya'] = $this->db->query("SELECT t.id_tagihan AS id, t.status, t.tgl_pembayaran AS waktu_bayar, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, j.tahun_ajaran, j.tenggat_waktu 
            FROM tagihan_siswa t 
            JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan 
            WHERE t.nis_siswa = '$id_siswa' AND j.nama_tagihan NOT LIKE '%SPP%' $where_tahun 
            ORDER BY t.id_tagihan ASC")->result();

        // Ambil list jenis transaksi (pembayaran), kecualikan yang mengandung kata SPP
        $data['jenis_transaksi'] = $this->db->query("SELECT kode_tagihan AS id, nama_tagihan AS nama, tahun_ajaran FROM jenis_tagihan WHERE nama_tagihan NOT LIKE '%SPP%'")->result();

        $data['id_siswa'] = $id_siswa;

	    $this->template->views('Backend/'.$this->parents.'/v_Detail',$data);
	}

    public function Simpan_Manual(){
        $insert = array(
            'nis_siswa'     => $this->input->post('id_siswa',TRUE),
            'kode_tagihan'  => $this->input->post('kode_tagihan',TRUE),
            'status'        => 'Belum Lunas'
        );

        $this->M_General->insert($this->table,$insert);
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function Bayar($id_tagihan) {
        $update = array(
            'status' => 'Lunas',
            'tgl_pembayaran' => waktu()
        );
        $this->M_General->update($this->table, $update, 'id_tagihan', $id_tagihan);
        
        // update kas
        $tagihan = $this->db->query("SELECT t.nis_siswa, j.nominal_tagihan AS nominal, j.nama_tagihan AS jenis_tagihan FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan WHERE t.id_tagihan = '$id_tagihan'")->row();
        if ($tagihan) {
            $this->M_General->update_kas('kas_masuk', $tagihan->nominal);
            
            // Send WhatsApp Notification
            $this->load->library('Wa_gateway');
            $this->wa_gateway->send_payment_confirmation($tagihan->nis_siswa, $tagihan->jenis_tagihan, $tagihan->nominal);
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
                $ceklunas = $this->db->query("SELECT t.nis_siswa, t.status, j.nominal_tagihan AS nominal, j.nama_tagihan AS jenis_tagihan FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan WHERE t.id_tagihan = '$id'")->row();
                if($ceklunas && $ceklunas->status == 'Belum Lunas') {
                    $update = array(
                        'status' => 'Lunas',
                        'tgl_pembayaran' => waktu()
                    );
                    $this->M_General->update($this->table, $update, 'id_tagihan', $id);
                    $this->M_General->update_kas('kas_masuk', $ceklunas->nominal);

                    // Send WhatsApp Notification
                    $this->wa_gateway->send_payment_confirmation($ceklunas->nis_siswa, $ceklunas->jenis_tagihan, $ceklunas->nominal);
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
            $tagihan = $this->db->query("SELECT t.*, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, s.nama_siswa AS name, s.nis_siswa AS nis FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan JOIN siswa s ON t.nis_siswa = s.nis_siswa WHERE t.id_tagihan IN ($id)")->result();
        } else {
            $tagihan = $this->db->query("SELECT t.*, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, s.nama_siswa AS name, s.nis_siswa AS nis FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan JOIN siswa s ON t.nis_siswa = s.nis_siswa WHERE t.id_tagihan = '$id'")->result();
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
            $pdf->Cell(50, 7, date('d-m-Y', strtotime($t->tgl_pembayaran)), 1, 0, 'C');
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
        $this->M_General->delete($this->table, 'id_tagihan', $id);
        $data['status'] = TRUE;
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function Hapus_Multi() {
        $ids = $this->input->post('ids');
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $this->M_General->delete($this->table, 'id_tagihan', $id);
            }
            $data['status'] = TRUE;
        } else {
            $data['status'] = FALSE;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

}
