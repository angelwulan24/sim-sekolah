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

		$data['siswa'] = $this->db->query("SELECT s.nis AS nis, s.nama_siswa AS name, s.jk_siswa AS sex, s.telp_siswa AS telpon, s.foto_siswa AS foto, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON s.id_kelas = k.id_kelas WHERE s.nis = '$id_siswa'")->row();
        
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
            $tagihan = $this->db->query("SELECT t.*, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, s.nama_siswa AS name, s.nis AS nis, k.nama_kelas FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan JOIN siswa s ON t.nis_siswa = s.nis LEFT JOIN kelas k ON s.id_kelas = k.id_kelas WHERE t.id_tagihan IN ($id)")->result();
        } else {
            $tagihan = $this->db->query("SELECT t.*, j.nama_tagihan AS jenis_tagihan, j.nominal_tagihan AS nominal, s.nama_siswa AS name, s.nis AS nis, k.nama_kelas FROM tagihan_siswa t JOIN jenis_tagihan j ON t.kode_tagihan = j.kode_tagihan JOIN siswa s ON t.nis_siswa = s.nis LEFT JOIN kelas k ON s.id_kelas = k.id_kelas WHERE t.id_tagihan = '$id'")->result();
        }

        if(count($tagihan) == 0) {
            show_error('Data pembayaran tidak ditemukan');
            return;
        }
        $t_first = $tagihan[0];

		$pdf = new FPDF('L', 'mm', 'A5');
        $pdf->SetMargins(10, 8, 10);
        $pdf->SetAutoPageBreak(false);
		$pdf->AddPage();
		
        // 1. Kop Surat (Header)
        $pdf->Image(FCPATH.'assets/dist/img/MI.png', 12, 8, 18, 18);
        
        $pdf->SetFont('TIMES', 'B', 10);
        $pdf->Cell(22);
        $pdf->Cell(0, 4.5, 'YAYASAN PENDIDIKAN ISLAM', 0, 1, 'L');
        
        $pdf->SetFont('TIMES', 'B', 13);
        $pdf->Cell(22);
        $pdf->Cell(0, 5.5, 'MADRASATUL QURAN DAAR EL-MUFLIHIN', 0, 1, 'L');
        
        $pdf->SetFont('TIMES', '', 8);
        $pdf->Cell(22);
        $pdf->Cell(0, 4, 'Perum Cikande Permai Blok G7/01 RT. 06/4 Kec. Cikande Kab. Serang', 0, 1, 'L');
        
        $pdf->Cell(22);
        $pdf->Cell(0, 4, 'Telp. 0823-1138-8825 - Email: midaarelmuflihin@gmail.com', 0, 1, 'L');
        
        // Double lines below header
        $pdf->Ln(2);
        $pdf->SetLineWidth(0.6);
        $pdf->Line(10, 29, 200, 29);
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, 30, 200, 30);
        
        // 2. Title
        $pdf->Ln(4);
        $pdf->SetFont('TIMES', 'BU', 11);
        $pdf->Cell(0, 5, 'BUKTI PEMBAYARAN SISWA', 0, 1, 'C');
        $pdf->Ln(3);
        
        // 3. Info Siswa
        $pdf->SetFont('TIMES', '', 9.5);
        
        // Nama Siswa
        $pdf->Cell(22, 5.5, 'Nama Siswa', 0, 0);
        $pdf->Cell(3, 5.5, ':', 0, 0);
        $pdf->Cell(95, 5.5, $t_first->name, 0, 0);
        
        // Tanggal
        $pdf->Cell(15, 5.5, 'Tanggal', 0, 0);
        $pdf->Cell(3, 5.5, ':', 0, 0);
        $tgl_bayar = $t_first->tgl_pembayaran ? tanggal($t_first->tgl_pembayaran, 'bulan') : '-';
        $pdf->Cell(0, 5.5, $tgl_bayar, 0, 1);
        
        // Kelas
        $pdf->Cell(22, 5.5, 'Kelas', 0, 0);
        $pdf->Cell(3, 5.5, ':', 0, 0);
        $pdf->Cell(95, 5.5, $t_first->nama_kelas ? $t_first->nama_kelas : '-', 0, 1);
        $pdf->Ln(2.5);
        
        // 4. Tabel Transaksi
        $pdf->SetFont('TIMES', 'B', 9);
        // Table Header
        $pdf->Cell(10, 6, 'No.', 'TB', 0, 'C');
        $pdf->Cell(130, 6, 'Keterangan Pembayaran', 'TB', 0, 'L');
        $pdf->Cell(50, 6, 'Jumlah (Rp)', 'TB', 1, 'R');
        
        $pdf->SetFont('TIMES', '', 9);
        $no = 1;
        $total_bayar = 0;
        $max_rows = 5;
        $total_items = count($tagihan);
        $last_row_index = max($max_rows, $total_items);
        
        // Loop paid items
        foreach ($tagihan as $row) {
            $border = ($no == $last_row_index) ? 'B' : 0;
            $pdf->Cell(10, 5.5, $no . '.', $border, 0, 'C');
            $pdf->Cell(130, 5.5, $row->jenis_tagihan, $border, 0, 'L');
            $pdf->Cell(50, 5.5, number_format($row->nominal, 0, ',', '.'), $border, 1, 'R');
            $total_bayar += $row->nominal;
            $no++;
        }
        
        // Draw empty rows up to 5 rows if items are fewer, to match the example image
        for ($i = $no; $i <= $max_rows; $i++) {
            $border = ($i == $last_row_index) ? 'B' : 0;
            $pdf->Cell(10, 5.5, $i . '.', $border, 0, 'C');
            $pdf->Cell(130, 5.5, '', $border, 0, 'L');
            $pdf->Cell(50, 5.5, '', $border, 1, 'R');
        }
        
        // 5. Grand Total
        $pdf->Ln(1.5);
        $pdf->SetFont('TIMES', 'B', 9.5);
        $pdf->Cell(140, 6, 'Grand Total:', 0, 0, 'R');
        
        $pdf->Cell(50, 6, number_format($total_bayar, 0, ',', '.'), 0, 1, 'R');
        $pdf->Ln(2);
        
        // 6. Signatures (Tanda Tangan)
        $pdf->SetFont('TIMES', '', 9);
        $pdf->Cell(130);
        $pdf->Cell(60, 4, 'Cikande, ' . ($t_first->tgl_pembayaran ? tanggal($t_first->tgl_pembayaran, 'bulan') : tanggal(waktu(), 'bulan')), 0, 1, 'C');
        
        $pdf->Cell(60, 4.5, 'Mengetahui,', 0, 0, 'C');
        $pdf->Cell(70);
        $pdf->Cell(60, 4.5, 'Yang Menerima,', 0, 1, 'C');
        
        $pdf->Ln(12); // space for signature
        
        $pdf->SetFont('TIMES', 'B', 9);
        $pdf->Cell(60, 4.5, 'Kh.Satibi Salim, M.Pd.I', 0, 0, 'C');
        $pdf->Cell(70);
        $pdf->Cell(60, 4.5, 'Nani Nuraeni S.Pd', 0, 1, 'C');
        
        // 7. Catatan (Notes)
        $pdf->Ln(1);
        $pdf->SetFont('TIMES', 'I', 7.5);
        $pdf->Cell(0, 3.5, 'Catatan :', 0, 1);
        $pdf->Cell(0, 3.5, '- Disimpan sebagai bukti pembayaran yang SAH', 0, 1);
		
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
