<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Laporan extends CI_Model {

	function getAllData(){
		$this->datatables->select("id,saldo_awal,DATE_FORMAT(tanggal,'%d-%m-%Y') as tanggal,,kas_masuk,kas_keluar, (saldo_awal + kas_masuk - kas_keluar) as saldo_akhir");
		$this->datatables->from('laporan');
		$this->datatables->add_column('view','<center><a href="javascript:void(0)" onclick="Detail($1)" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Detail</a> </center> ','id');
		return $this->datatables->generate();
	}
	


    function Cetak_periode($data, $awal, $akhir){
        $this->load->library('pdf');
        $this->load->helper('data'); // Ensure helper is loaded
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        
       // Header
       $pdf->Cell(3,5,'',0,1);
       $pdf->Image(base_url().'/assets/dist/img/MI.png', 10, 10,33);
       $pdf->Cell(3,-5,'',0,1);
       $pdf->SetFont('TIMES','B',14);
       $pdf->Cell(189, 5, 'KEMENTRIAN AGAMA REPUBLIK INDONESIA', 0, 1, 'C');
       $pdf->Cell(189, 7, 'KANTOR KEMENTRIAN AGAMA KABUPATEN PATI', 0, 1, 'C');
       $pdf->SetFont('TIMES','B',16);
       $pdf->Cell(192, 7, 'MADRASAH ALIYAH NEGERI PATI', 0, 1, 'C');
       $pdf->SetFont('TIMES','',12);
       $pdf->Cell(189, 5, 'Jl. Ratu kalinyamat Gg. Melati II, Kec. Tayu, Kabupaten Pati', 0, 1, 'C');
       $pdf->Cell(189, 5, 'Telp.(020) 0000000,Fax(020)0000000', 0, 1, 'C');
       $pdf->Cell(189, 5, 'E-mail : madrasahaliyah@gmail.com', 0, 1, 'C');
       $pdf->SetLineWidth(1);
       $pdf->Line(9, 46, 203, 46);
       $pdf->SetLineWidth(0);
       $pdf->Line(9, 47, 203, 47);
       
       $pdf->Cell(3,8,'',0,1);
       $pdf->SetFont('TIMES','',12);
       $pdf->Cell(0,5,'Periode: '.date('d-m-Y', strtotime($awal)).' s/d '.date('d-m-Y', strtotime($akhir)),0,1,'C');
       $pdf->Ln(5);

        // Header Table
        $pdf->SetFont('TIMES','B',10);
        $pdf->Cell(10,8,'No',1,0,'C');
        $pdf->Cell(30,8,'Tanggal',1,0,'C');
        $pdf->Cell(35,8,'Saldo Awal',1,0,'C');
        $pdf->Cell(35,8,'Kas Masuk',1,0,'C');
        $pdf->Cell(35,8,'Kas Keluar',1,0,'C');
        $pdf->Cell(35,8,'Saldo Akhir',1,1,'C');

        // Content
        $pdf->SetFont('TIMES','',10);
        $no = 1;
        $total_masuk = 0;
        $total_keluar = 0;

        foreach($data as $row){
            $saldo_akhir = $row->saldo_awal + $row->kas_masuk - $row->kas_keluar;
            
            $pdf->Cell(10,8,$no++,1,0,'C');
            $pdf->Cell(30,8,date('d-m-Y', strtotime($row->tanggal)),1,0,'C');
            $pdf->Cell(35,8,number_format($row->saldo_awal,0,',','.'),1,0,'R');
            $pdf->Cell(35,8,number_format($row->kas_masuk,0,',','.'),1,0,'R');
            $pdf->Cell(35,8,number_format($row->kas_keluar,0,',','.'),1,0,'R');
            $pdf->Cell(35,8,number_format($saldo_akhir,0,',','.'),1,1,'R');
            
            $total_masuk += $row->kas_masuk;
            $total_keluar += $row->kas_keluar;
        }

        // Summary
        $pdf->SetFont('TIMES','B',10);
        $pdf->Cell(75,8,'Total',1,0,'R');
        $pdf->Cell(35,8,number_format($total_masuk,0,',','.'),1,0,'R');
        $pdf->Cell(35,8,number_format($total_keluar,0,',','.'),1,0,'R');
        $pdf->Cell(35,8,'-',1,1,'R');

       // Footer / Signature
       $pdf->SetFont('TIMES','',12);
       $pdf->Cell(125, 35, '', 0, 1);
       $pdf->Cell(125, 35, '', 0, 0);
       $pdf->Cell(55, 5, 'Pati, '.  tanggal(waktu(),'bulan'), 0, 1);
       $pdf->Cell(125, 5, '', 0, 0);
       $pdf->Cell(35, 5, 'Kepala Yayasan,', 0, 1);
       $pdf->Cell(125, 10, '', 0, 0);
       $pdf->Cell(35, 14, '', 0, 1);
       $pdf->Cell(125, 8, '', 0, 0);
       $pdf->Cell(35, 9, 'Drs. Soetarmo, M.Pd.', 0, 0);

        $pdf->Output();
    }



    function Cetak_detail($data){
        $this->load->library('pdf');
        $this->load->helper('data');
        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        
       // Header
       $pdf->Cell(3,5,'',0,1);
       $pdf->Image(base_url().'/assets/dist/img/MI.png', 10, 10,33);
       $pdf->Cell(3,-5,'',0,1);
       $pdf->SetFont('TIMES','B',14);
       $pdf->Cell(189, 5, 'KEMENTRIAN AGAMA REPUBLIK INDONESIA', 0, 1, 'C');
       $pdf->Cell(189, 7, 'KANTOR KEMENTRIAN AGAMA KABUPATEN PATI', 0, 1, 'C');
       $pdf->SetFont('TIMES','B',16);
       $pdf->Cell(192, 7, 'MADRASAH ALIYAH NEGERI PATI', 0, 1, 'C');
       $pdf->SetFont('TIMES','',12);
       $pdf->Cell(189, 5, 'Jl. Ratu kalinyamat Gg. Melati II, Kec. Tayu, Kabupaten Pati', 0, 1, 'C');
       $pdf->Cell(189, 5, 'Telp.(020) 0000000,Fax(020)0000000', 0, 1, 'C');
       $pdf->Cell(189, 5, 'E-mail : madrasahaliyah@gmail.com', 0, 1, 'C');
       $pdf->SetLineWidth(1);
       $pdf->Line(9, 46, 203, 46);
       $pdf->SetLineWidth(0);
       $pdf->Line(9, 47, 203, 47);
       
       $pdf->Cell(3,8,'',0,1);
       $pdf->SetFont('TIMES','',12);
       $pdf->Cell(0,5,'Tanggal: '.date('d-m-Y', strtotime($data['tanggal'])),0,1,'C');
       $pdf->Ln(5);

        $total_pemasukan = 0;

        // Pendaftaran
        if(!empty($data['pendaftaran'])) {
            $pdf->SetFillColor(255, 255, 0);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(0, 8, 'UANG PENDAFTARAN', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['pendaftaran'] as $row) {
                $pdf->Cell(100, 7, $row->siswa, 1);
                $pdf->Cell(30, 7, 'Rp.', 1);
                $pdf->Cell(60, 7, number_format($row->nominal,0,',','.'), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total', 1);
            $pdf->Cell(60, 7, number_format($subtotal,0,',','.'), 1, 1, 'R');
        }

        // Ujian
        if(!empty($data['ujian'])) {
            $pdf->SetFillColor(255, 255, 0);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(0, 8, 'UANG UJIAN', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['ujian'] as $row) {
                $pdf->Cell(100, 7, $row->name . ' (' . $row->periode . ')', 1);
                $pdf->Cell(30, 7, 'Rp.', 1);
                $pdf->Cell(60, 7, number_format($row->nominal,0,',','.'), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total', 1);
            $pdf->Cell(60, 7, number_format($subtotal,0,',','.'), 1, 1, 'R');
        }

        // SPP
        if(!empty($data['spp'])) {
            $pdf->SetFillColor(255, 255, 0);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(0, 8, 'UANG SPP', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['spp'] as $row) {
                $pdf->Cell(100, 7, $row->name . ' (' . $row->bulan . ')', 1);
                $pdf->Cell(30, 7, 'Rp.', 1);
                $pdf->Cell(60, 7, number_format($row->nominal,0,',','.'), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total', 1);
            $pdf->Cell(60, 7, number_format($subtotal,0,',','.'), 1, 1, 'R');
        }

         // Buku (Snack)
         if(!empty($data['buku'])) {
            $pdf->SetFillColor(255, 255, 0);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(0, 8, 'UANG BUKU/SNACK', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['buku'] as $row) {
                $pdf->Cell(100, 7, $row->name . ' (' . $row->jumlah . ' Hari)', 1);
                $pdf->Cell(30, 7, 'Rp.', 1);
                $pdf->Cell(60, 7, number_format($row->total,0,',','.'), 1, 1, 'R');
                $subtotal += $row->total;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total', 1);
            $pdf->Cell(60, 7, number_format($subtotal,0,',','.'), 1, 1, 'R');
        }

        // Baju (Catering)
        if(!empty($data['baju'])) {
            $pdf->SetFillColor(255, 255, 0);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(0, 8, 'UANG BAJU/CATERING', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['baju'] as $row) {
                $pdf->Cell(100, 7, $row->name . ' (' . $row->jumlah . ' Hari)', 1);
                $pdf->Cell(30, 7, 'Rp.', 1);
                $pdf->Cell(60, 7, number_format($row->total,0,',','.'), 1, 1, 'R');
                $subtotal += $row->total;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total', 1);
            $pdf->Cell(60, 7, number_format($subtotal,0,',','.'), 1, 1, 'R');
        }

        // Lainnya (Pemasukan)
        if(!empty($data['pemasukan'])) {
            $pdf->SetFillColor(255, 255, 0);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(0, 8, 'PEMASUKAN LAINNYA', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['pemasukan'] as $row) {
                $pdf->Cell(100, 7, $row->keterangan, 1);
                $pdf->Cell(30, 7, 'Rp.', 1);
                $pdf->Cell(60, 7, number_format($row->nominal,0,',','.'), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pemasukan += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total', 1);
            $pdf->Cell(60, 7, number_format($subtotal,0,',','.'), 1, 1, 'R');
        }

        $pdf->Ln(5);
        $pdf->SetFont('TIMES','B',12);
        $pdf->Cell(130, 8, 'TOTAL PEMASUKAN', 1, 0, 'C');
        $pdf->Cell(60, 8, number_format($total_pemasukan,0,',','.'), 1, 1, 'R');
        $pdf->Ln(10);


        // PENGELUARAN
        $total_pengeluaran = 0;
        
        // Gaji
        if(!empty($data['gaji'])) {
            $pdf->SetFillColor(0, 204, 255);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(0, 8, 'GAJI GURU', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['gaji'] as $row) {
                $pdf->Cell(100, 7, $row->name . ' (' . $row->periode . ')', 1);
                $pdf->Cell(30, 7, 'Rp.', 1);
                $pdf->Cell(60, 7, number_format($row->gaji,0,',','.'), 1, 1, 'R');
                $subtotal += $row->gaji;
            }
            $total_pengeluaran += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total', 1);
            $pdf->Cell(60, 7, number_format($subtotal,0,',','.'), 1, 1, 'R');
        }

        // Pengeluaran Lainnya
        if(!empty($data['pengeluaran'])) {
            $pdf->SetFillColor(0, 204, 255);
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(0, 8, 'PENGELUARAN LAINNYA', 1, 1, 'L', true);
            $pdf->SetFont('TIMES','',10);
            $subtotal = 0;
            foreach($data['pengeluaran'] as $row) {
                $pdf->Cell(100, 7, $row->keterangan, 1);
                $pdf->Cell(30, 7, 'Rp.', 1);
                $pdf->Cell(60, 7, number_format($row->nominal,0,',','.'), 1, 1, 'R');
                $subtotal += $row->nominal;
            }
            $total_pengeluaran += $subtotal;
            $pdf->SetFont('TIMES','B',10);
            $pdf->Cell(130, 7, 'Sub Total', 1);
            $pdf->Cell(60, 7, number_format($subtotal,0,',','.'), 1, 1, 'R');
        }

        $pdf->Ln(5);
        $pdf->SetFont('TIMES','B',12);
        $pdf->Cell(130, 8, 'TOTAL PENGELUARAN', 1, 0, 'C');
        $pdf->Cell(60, 8, number_format($total_pengeluaran,0,',','.'), 1, 1, 'R');

       // Footer / Signature
       $pdf->SetFont('TIMES','',12);
       $pdf->Cell(125, 35, '', 0, 1);
       $pdf->Cell(125, 35, '', 0, 0);
       $pdf->Cell(55, 5, 'Pati, '.  tanggal(waktu(),'bulan'), 0, 1);
       $pdf->Cell(125, 5, '', 0, 0);
       $pdf->Cell(35, 5, 'Kepala Yayasan,', 0, 1);
       $pdf->Cell(125, 10, '', 0, 0);
       $pdf->Cell(35, 14, '', 0, 1);
       $pdf->Cell(125, 8, '', 0, 0);
       $pdf->Cell(35, 9, 'Drs. Soetarmo, M.Pd.', 0, 0);

        $pdf->Output();
    }
}
