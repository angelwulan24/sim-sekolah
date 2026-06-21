<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Laporan extends CI_Model {

	function getAllData($filter = array()){
		$this->datatables->select("id,saldo_awal,DATE_FORMAT(tanggal,'%d-%m-%Y') as tanggal,kas_masuk,kas_keluar, (saldo_awal + kas_masuk - kas_keluar) as saldo_akhir");
		$this->datatables->from('laporan');

		if(!empty($filter['jenis']) && !empty($filter['tanggal'])){
            if($filter['jenis'] == 'hari'){
                $this->datatables->where('DATE(tanggal)', $filter['tanggal']);
            } elseif($filter['jenis'] == 'bulan'){
                $this->datatables->where("DATE_FORMAT(tanggal, '%Y-%m') = ", $filter['tanggal']);
            } elseif($filter['jenis'] == 'tahun'){
                $this->datatables->where("YEAR(tanggal)", $filter['tanggal']);
            }
		}

		$this->datatables->add_column('view','<center><a href="javascript:void(0)" onclick="Detail($1)" class="btn btn-warning btn-xs"><i class="fa fa-eye"></i> Detail</a> </center> ','id');
		return $this->datatables->generate();
	}
	


    function Cetak_periode($data, $awal, $akhir){
        $this->load->library('pdf');
        $this->load->helper('data');

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
        $pdf->Cell(190, 7, 'Laporan Kas Masuk dan Keluar Periode : '.tanggal($awal,'bulan').' - '.tanggal($akhir,'bulan'), 0, 1, 'C');
        $pdf->Ln(5);

        // Header Table
        $pdf->SetFont('TIMES','B',10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Tanggal', 1, 0, 'C', true);
        $pdf->Cell(37, 8, 'Saldo Awal', 1, 0, 'C', true);
        $pdf->Cell(37, 8, 'Kas Masuk', 1, 0, 'C', true);
        $pdf->Cell(37, 8, 'Kas Keluar', 1, 0, 'C', true);
        $pdf->Cell(34, 8, 'Saldo Akhir', 1, 1, 'C', true);

        // Content
        $pdf->SetFont('TIMES','',10);
        $no = 1;
        $total_masuk = 0;
        $total_keluar = 0;

        foreach($data as $row){
            $saldo_akhir = $row->saldo_awal + $row->kas_masuk - $row->kas_keluar;
            
            $pdf->Cell(10, 8, $no++, 1, 0, 'C');
            $pdf->Cell(35, 8, date('d-m-Y', strtotime($row->tanggal)), 1, 0, 'C');
            $pdf->Cell(37, 8, rupiah($row->saldo_awal), 1, 0, 'R');
            $pdf->Cell(37, 8, rupiah($row->kas_masuk), 1, 0, 'R');
            $pdf->Cell(37, 8, rupiah($row->kas_keluar), 1, 0, 'R');
            $pdf->Cell(34, 8, rupiah($saldo_akhir), 1, 1, 'R');
            
            $total_masuk += $row->kas_masuk;
            $total_keluar += $row->kas_keluar;
        }

        // Summary
        $pdf->SetFont('TIMES','B',10);
        $pdf->Cell(82, 8, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(37, 8, rupiah($total_masuk), 1, 0, 'R', true);
        $pdf->Cell(37, 8, rupiah($total_keluar), 1, 0, 'R', true);
        $pdf->Cell(34, 8, '-', 1, 1, 'C', true);

        $pdf->Ln(15);
        $pdf->SetFont('TIMES', '', 11);
        $pdf->Cell(130);
        $pdf->Cell(60, 5, 'Cikande Permai, ' . tanggal(waktu(), 'bulan'), 0, 1, 'C');
        $pdf->Cell(130);
        $pdf->Cell(60, 5, 'Kepala Yayasan,', 0, 1, 'C');
        $pdf->Ln(20);
        $pdf->Cell(130);
        $pdf->SetFont('TIMES', 'B', 11);
        $pdf->Cell(60, 5, 'Kh.Satibi Salim, M.Pd.I', 0, 1, 'C');

        $pdf->Output();
    }



    function Cetak_detail($data){
        $this->load->library('pdf');
        $this->load->helper('data');
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
        $pdf->Cell(190, 7, 'RINCIAN DETAIL TRANSAKSI HARIAN', 0, 1, 'C');
        $pdf->SetFont('TIMES','',11);
        $pdf->Cell(190, 7, 'Tanggal: '.tanggal($data['tanggal'],'bulan'), 0, 1, 'C');
        $pdf->Ln(5);

        $total_pemasukan = 0;

        // Pendaftaran
        if(!empty($data['pendaftaran'])) {
            $pdf->SetFillColor(230, 255, 230);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(190, 8, ' [ PEMASUKAN ] UANG PENDAFTARAN', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['pendaftaran'] as $row) {
                $pdf->Cell(130, 7, $row->siswa, 1, 0, 'L');
                $pdf->Cell(60, 7, rupiah($row->nominal), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total Pendaftaran', 1, 0, 'R', true);
            $pdf->Cell(60, 7, rupiah($subtotal), 1, 1, 'R', true);
        }

        // Ujian
        if(!empty($data['ujian'])) {
            $pdf->SetFillColor(230, 255, 230);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(190, 8, ' [ PEMASUKAN ] UANG UJIAN', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['ujian'] as $row) {
                $pdf->Cell(130, 7, $row->name . ' (' . $row->periode . ')', 1, 0, 'L');
                $pdf->Cell(60, 7, rupiah($row->nominal), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total Ujian', 1, 0, 'R', true);
            $pdf->Cell(60, 7, rupiah($subtotal), 1, 1, 'R', true);
        }

        // SPP
        if(!empty($data['spp'])) {
            $pdf->SetFillColor(230, 255, 230);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(190, 8, ' [ PEMASUKAN ] UANG SPP', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['spp'] as $row) {
                $pdf->Cell(130, 7, $row->name . ' (' . $row->bulan . ')', 1, 0, 'L');
                $pdf->Cell(60, 7, rupiah($row->nominal), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total SPP', 1, 0, 'R', true);
            $pdf->Cell(60, 7, rupiah($subtotal), 1, 1, 'R', true);
        }

         // Buku
         if(!empty($data['buku'])) {
            $pdf->SetFillColor(230, 255, 230);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(190, 8, ' [ PEMASUKAN ] UANG BUKU', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['buku'] as $row) {
                $pdf->Cell(130, 7, $row->name, 1, 0, 'L');
                $pdf->Cell(60, 7, rupiah($row->total), 1, 1, 'R');
                $subtotal += $row->total;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total Buku', 1, 0, 'R', true);
            $pdf->Cell(60, 7, rupiah($subtotal), 1, 1, 'R', true);
        }

        // Baju
        if(!empty($data['baju'])) {
            $pdf->SetFillColor(230, 255, 230);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(190, 8, ' [ PEMASUKAN ] UANG BAJU', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['baju'] as $row) {
                $pdf->Cell(130, 7, $row->name, 1, 0, 'L');
                $pdf->Cell(60, 7, rupiah($row->total), 1, 1, 'R');
                $subtotal += $row->total;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total Baju', 1, 0, 'R', true);
            $pdf->Cell(60, 7, rupiah($subtotal), 1, 1, 'R', true);
        }

        // Lainnya (Pemasukan)
        if(!empty($data['pemasukan'])) {
            $pdf->SetFillColor(230, 255, 230);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(190, 8, ' [ PEMASUKAN ] LAINNYA', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['pemasukan'] as $row) {
                $pdf->Cell(130, 7, $row->keterangan, 1, 0, 'L');
                $pdf->Cell(60, 7, rupiah($row->nominal), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total Lainnya', 1, 0, 'R', true);
            $pdf->Cell(60, 7, rupiah($subtotal), 1, 1, 'R', true);
        }

        $pdf->Ln(5);
        $pdf->SetFont('TIMES','B',11);
        $pdf->SetFillColor(200, 255, 200);
        $pdf->Cell(130, 9, 'T O T A L   P E M A S U K A N', 1, 0, 'C', true);
        $pdf->Cell(60, 9, rupiah($total_pemasukan), 1, 1, 'R', true);
        $pdf->Ln(10);


        // PENGELUARAN
        $total_pengeluaran = 0;
        
        // Gaji
        if(!empty($data['gaji'])) {
            $pdf->SetFillColor(255, 230, 230);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(190, 8, ' [ PENGELUARAN ] GAJI GURU', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['gaji'] as $row) {
                $pdf->Cell(130, 7, $row->name . ' (' . $row->periode . ')', 1, 0, 'L');
                $pdf->Cell(60, 7, rupiah($row->gaji), 1, 1, 'R');
                $subtotal += $row->gaji;
            }
            $total_pengeluaran += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total Gaji', 1, 0, 'R', true);
            $pdf->Cell(60, 7, rupiah($subtotal), 1, 1, 'R', true);
        }

        // Pengeluaran Lainnya
        if(!empty($data['pengeluaran'])) {
            $pdf->SetFillColor(255, 230, 230);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(190, 8, ' [ PENGELUARAN ] LAINNYA', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['pengeluaran'] as $row) {
                $pdf->Cell(130, 7, $row->keterangan, 1, 0, 'L');
                $pdf->Cell(60, 7, rupiah($row->nominal), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pengeluaran += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total Lainnya', 1, 0, 'R', true);
            $pdf->Cell(60, 7, rupiah($subtotal), 1, 1, 'R', true);
        }

        $pdf->Ln(5);
        $pdf->SetFont('TIMES','B',11);
        $pdf->SetFillColor(255, 200, 200);
        $pdf->Cell(130, 9, 'T O T A L   P E N G E L U A R A N', 1, 0, 'C', true);
        $pdf->Cell(60, 9, rupiah($total_pengeluaran), 1, 1, 'R', true);

        $pdf->Ln(15);
        $pdf->SetFont('TIMES', '', 11);
        $pdf->Cell(130);
        $pdf->Cell(60, 5, 'Cikande Permai, ' . tanggal(waktu(), 'bulan'), 0, 1, 'C');
        $pdf->Cell(130);
        $pdf->Cell(60, 5, 'Kepala Yayasan,', 0, 1, 'C');
        $pdf->Ln(20);
        $pdf->Cell(130);
        $pdf->SetFont('TIMES', 'B', 11);
        $pdf->Cell(60, 5, 'Kh.Satibi Salim, M.Pd.I', 0, 1, 'C');

        $pdf->Output();
    }
}
