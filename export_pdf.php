<?php
include "koneksi.php";
require('fpdf/fpdf.php');

$pdf = new FPDF('L','mm','A4'); // Landscape
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Laporan Keuangan Pribadi',0,1,'C');
$pdf->Ln(5);

// Header tabel
$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,10,'Tanggal',1,0,'C');
$pdf->Cell(30,10,'Gaji',1,0,'C');
$pdf->Cell(25,10,'Makan',1,0,'C');
$pdf->Cell(25,10,'Kopi',1,0,'C');
$pdf->Cell(25,10,'Bensin',1,0,'C');
$pdf->Cell(40,10,'Tambahan',1,0,'C');
$pdf->Cell(40,10,'Total Pengeluaran',1,0,'C');
$pdf->Cell(30,10,'Sisa',1,0,'C');
$pdf->Cell(30,10,'Target',1,0,'C');
$pdf->Cell(30,10,'Status',1,1,'C');

// Data
$pdf->SetFont('Arial','',9);
$total_gaji = 0;
$total_pengeluaran = 0;
$total_sisa = 0;

$sql = $koneksi->query("SELECT * FROM transaksi ORDER BY tanggal DESC");
while ($row = $sql->fetch_assoc()) {
    $id      = $row['id'];
    $tanggal = $row['tanggal'];
    $gaji    = $row['gaji'];
    $makan   = $row['makan'];
    $kopi    = $row['kopi'];
    $bensin  = $row['bensin'];

    // Hitung pengeluaran tambahan (pakai jumlah)
    $extra_q = $koneksi->query("
        SELECT SUM(jumlah) as total_extra 
        FROM pengeluaran 
        WHERE transaksi_id='$id'
    ");
    $extra_row = $extra_q->fetch_assoc();
    $lainnya = $extra_row['total_extra'] ?? 0;

    $pengeluaran = $makan + $kopi + $bensin + $lainnya;
    $sisa = $gaji - $pengeluaran;

    // Cek target tabungan
    $bulan = date("n", strtotime($tanggal));
    $tahun = date("Y", strtotime($tanggal));
    $target_q = $koneksi->query("SELECT jumlah FROM target_tabungan WHERE bulan='$bulan' AND tahun='$tahun'");
    $target_row = $target_q->fetch_assoc();
    $target = $target_row['jumlah'] ?? 0;

    $status = ($sisa >= $target) ? "✅" : "❌";

    // Tulis ke tabel PDF
    $pdf->Cell(30,10,$tanggal,1,0,'C');
    $pdf->Cell(30,10,"Rp ".number_format($gaji,0,",","."),1,0,'R');
    $pdf->Cell(25,10,"Rp ".number_format($makan,0,",","."),1,0,'R');
    $pdf->Cell(25,10,"Rp ".number_format($kopi,0,",","."),1,0,'R');
    $pdf->Cell(25,10,"Rp ".number_format($bensin,0,",","."),1,0,'R');
    $pdf->Cell(40,10,"Rp ".number_format($lainnya,0,",","."),1,0,'R');
    $pdf->Cell(40,10,"Rp ".number_format($pengeluaran,0,",","."),1,0,'R');
    $pdf->Cell(30,10,"Rp ".number_format($sisa,0,",","."),1,0,'R');
    $pdf->Cell(30,10,"Rp ".number_format($target,0,",","."),1,0,'R');
    $pdf->Cell(30,10,$status,1,1,'C');

    $total_gaji += $gaji;
    $total_pengeluaran += $pengeluaran;
    $total_sisa += $sisa;
}

// Ringkasan
$pdf->Ln(5);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,10,"Total Gaji",1,0,'L');
$pdf->Cell(50,10,"Rp ".number_format($total_gaji,0,",","."),1,1,'R');
$pdf->Cell(50,10,"Total Pengeluaran",1,0,'L');
$pdf->Cell(50,10,"Rp ".number_format($total_pengeluaran,0,",","."),1,1,'R');
$pdf->Cell(50,10,"Total Sisa",1,0,'L');
$pdf->Cell(50,10,"Rp ".number_format($total_sisa,0,",","."),1,1,'R');

// Output PDF
ob_end_clean(); 
$pdf->Output("Laporan_Keuangan.pdf","D");
