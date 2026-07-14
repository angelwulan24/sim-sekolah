<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cetak extends CI_Controller{
    function __construct() {
        parent::__construct();
         $this->load->library('pdf');
          is_login();
    }

    function cetak_laporan($id){

      $data = $this->M_General->get_Laporan($id);
      $tgl = $data['tanggal'];
      $awal = $this->db->query("SELECT saldo_awal FROM laporan WHERE id = '$id'")->row_array();
      $cat = 0; $snak = 0; $sp = 0; $uji = 0; $pend = 0; $gaj = 0;
      
      if(!empty($data['baju'])){ foreach ($data['baju'] as $k) { $cat += $k->total; } }
      if(!empty($data['buku'])){ foreach ($data['buku'] as $k) { $snak += $k->total; } }
      if(!empty($data['spp'])){ foreach ($data['spp'] as $k) { $sp += $k->nominal; } }
      if(!empty($data['ujian'])){ foreach ($data['ujian'] as $k) { $uji += $k->nominal; } }
      if(!empty($data['pendaftaran'])){ foreach ($data['pendaftaran'] as $k) { $pend += $k->nominal; } }
      if(!empty($data['gaji'])){ foreach ($data['gaji'] as $k) { $gaj += $k->gaji; } }

      $pdf = new FPDF('p','mm','A4');
      $pdf->SetMargins(10, 10, 10);
      $pdf->AddPage();
      
      // Header
      $pdf->Image(FCPATH.'assets/dist/img/MI.png', 10, 12, 28);
      $pdf->SetFont('TIMES','B',12);
      $pdf->Cell(28); // Gap for logo
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
      $pdf->Cell(190, 7, 'Laporan Pemasukan dan Pengeluaran Pertanggal : '.tanggal($tgl,'bulan'), 0, 1, 'L');
      
      $pdf->Ln(5);
      $pdf->SetFont('TIMES','B',11);
      $pdf->Cell(50, 7, '- Saldo Awal', 0, 0);
      $pdf->SetFont('TIMES','B',12);
      $pdf->Cell(140, 7, rupiah($awal['saldo_awal']), 0, 1, 'R');
      
      $pdf->Ln(2);
      $pdf->SetFont('TIMES','B',11);
      $pdf->Cell(190, 7, '- Pemasukan', 0, 1);
      
      $pdf->SetFont('TIMES','B',10);
      $pdf->SetFillColor(240, 240, 240);
      $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
      $pdf->Cell(130, 7, 'Keterangan', 1, 0, 'C', true);
      $pdf->Cell(50, 7, 'Nominal', 1, 1, 'C', true);
      
      $pdf->SetFont('TIMES','',10);
      $pdf->Cell(10, 7, '1', 1, 0, 'C');
      $pdf->Cell(130, 7, 'Uang Catering', 1, 0, 'L');
      $pdf->Cell(50, 7, rupiah($cat), 1, 1, 'R');
      
      $pdf->Cell(10, 7, '2', 1, 0, 'C');
      $pdf->Cell(130, 7, 'Uang Snack', 1, 0, 'L');
      $pdf->Cell(50, 7, rupiah($snak), 1, 1, 'R');
      
      $pdf->Cell(10, 7, '3', 1, 0, 'C');
      $pdf->Cell(130, 7, 'Uang SPP', 1, 0, 'L');
      $pdf->Cell(50, 7, rupiah($sp), 1, 1, 'R');
      
      $pdf->Cell(10, 7, '4', 1, 0, 'C');
      $pdf->Cell(130, 7, 'Uang ujian', 1, 0, 'L');
      $pdf->Cell(50, 7, rupiah($uji), 1, 1, 'R');
      
      $pdf->Cell(10, 7, '5', 1, 0, 'C');
      $pdf->Cell(130, 7, 'Uang Pendaftaran', 1, 0, 'L');
      $pdf->Cell(50, 7, rupiah($pend), 1, 1, 'R');
      
      $t = 6; $pem = 0;
      if(!empty($data['pemasukan'])){
          foreach($data['pemasukan'] as $r){
              $pem += $r->nominal;
              $pdf->Cell(10, 7, $t++, 1, 0, 'C');
              $pdf->Cell(130, 7, $r->keterangan, 1, 0, 'L');
              $pdf->Cell(50, 7, rupiah($r->nominal), 1, 1, 'R');
          }
      }
      
      $sum = $pem+$cat+$snak+$sp+$uji+$pend;
      $pdf->SetFont('TIMES','B',10);
      $pdf->Cell(140, 7, 'Total Pemasukan', 1, 0, 'C', true);
      $pdf->Cell(50, 7, rupiah($sum), 1, 1, 'R', true);
      
      $pdf->Ln(5);
      $pdf->SetFont('TIMES','B',11);
      $pdf->Cell(190, 7, '- Pengeluaran', 0, 1);
      
      $pdf->SetFont('TIMES','B',10);
      $pdf->Cell(10, 7, 'No', 1, 0, 'C', true);
      $pdf->Cell(130, 7, 'Keterangan', 1, 0, 'C', true);
      $pdf->Cell(50, 7, 'Nominal', 1, 1, 'C', true);
      
      $pdf->SetFont('TIMES','',10);
      $pdf->Cell(10, 7, '1', 1, 0, 'C');
      $pdf->Cell(130, 7, 'Pembayaran Gaji', 1, 0, 'L');
      $pdf->Cell(50, 7, rupiah($gaj), 1, 1, 'R');
      
      $t = 2; $pen = 0;
      if(!empty($data['pengeluaran'])){
          foreach($data['pengeluaran'] as $r){
              $pen += $r->nominal;
              $pdf->Cell(10, 7, $t++, 1, 0, 'C');
              $pdf->Cell(130, 7, $r->keterangan, 1, 0, 'L');
              $pdf->Cell(50, 7, rupiah($r->nominal), 1, 1, 'R');
          }
      }
      
      $sum1 = $gaj+$pen;
      $pdf->SetFont('TIMES','B',10);
      $pdf->Cell(140, 7, 'Total Pengeluaran', 1, 0, 'C', true);
      $pdf->Cell(50, 7, rupiah($sum1), 1, 1, 'R', true);
      
      $akhir = $awal['saldo_awal']+$sum-$sum1;
      $pdf->Ln(5);
      $pdf->SetFont('TIMES','B',11);
      $pdf->Cell(50, 7, '- Saldo Akhir', 0, 0);
      $pdf->SetFont('TIMES','B',12);
      $pdf->Cell(140, 7, rupiah($akhir), 0, 1, 'R');
      
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
      $pdf->Output('D', 'Laporan_' . $tgl . '.pdf');
    }

    function Cetak_periode(){
        $awal_tgl = $this->input->post('awal');
        $akhir_tgl = $this->input->post('akhir');

        $this->db->where('tanggal >=',$awal_tgl);
        $this->db->where('tanggal <=',$akhir_tgl);
        $a = $this->db->get('laporan')->result();

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
        $pdf->SetFont('TIMES','',12);
        $pdf->Cell(190, 7, 'Laporan Pemasukan dan Pengeluaran Periode : '.tanggal($awal_tgl,'bulan').' - '.tanggal($akhir_tgl,'bulan'), 0, 1);

        $pdf->Ln(5);
        $pdf->SetFont('TIMES','B',10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Tanggal', 1, 0, 'C', true);
        $pdf->Cell(37, 8, 'Saldo Awal', 1, 0, 'C', true);
        $pdf->Cell(37, 8, 'Pemasukan', 1, 0, 'C', true);
        $pdf->Cell(37, 8, 'Pengeluaran', 1, 0, 'C', true);
        $pdf->Cell(34, 8, 'Saldo Akhir', 1, 1, 'C', true);
        
        $pdf->SetFont('TIMES','',10);
        $no = 1;
        foreach ($a as $i){
          $saldo = $i->saldo_awal + $i->kas_masuk - $i->kas_keluar;
           $pdf->Cell(10, 7, $no++, 1, 0, 'C');
           $pdf->Cell(35, 7, tanggal($i->tanggal,'bln'), 1, 0, 'C');
           $pdf->Cell(37, 7, rupiah($i->saldo_awal), 1, 0, 'R');
           $pdf->Cell(37, 7, rupiah($i->kas_masuk), 1, 0, 'R');
           $pdf->Cell(37, 7, rupiah($i->kas_keluar), 1, 0, 'R');
           $pdf->Cell(34, 7, rupiah($saldo), 1, 1, 'R');
        }

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
        $pdf->Output('D', 'Laporan_Periode_' . $awal_tgl . '_to_' . $akhir_tgl . '.pdf');  
    }
}