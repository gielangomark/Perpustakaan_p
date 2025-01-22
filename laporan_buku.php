<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

require 'koneksi.php';

// Get all books
$buku = $conn->query("SELECT * FROM buku ORDER BY id ASC");

// Set header for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="laporan_buku.pdf"');

require('fpdf186/fpdf.php');

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Laporan Daftar Buku Perpustakaan', 0, 1, 'C');
        $this->Ln(10);
        
        // Header Tabel
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(10, 10, 'No', 1, 0, 'C'); // Ubah "ID" menjadi "No"
        $this->Cell(50, 10, 'Judul', 1, 0, 'C');
        $this->Cell(40, 10, 'Cover', 1, 0, 'C');
        $this->Cell(50, 10, 'Pengarang', 1, 0, 'C');
        $this->Cell(30, 10, 'Tahun', 1, 1, 'C');
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L'); // Gunakan orientasi landscape
$pdf->SetFont('Arial', '', 10);

$counter = 1; // Inisialisasi counter manual

while($row = $buku->fetch_assoc()) {
    $currentY = $pdf->GetY();
    $rowHeight = 30; // Tinggi baris untuk mengakomodasi gambar
    
    // Cek apakah halaman baru diperlukan
    if($currentY + $rowHeight > $pdf->GetPageHeight() - 20) {
        $pdf->AddPage('L');
    }
    $pdf->Cell(10, $rowHeight, $counter++, 1, 0, 'C'); // Gunakan counter manual untuk nomor urut
    $pdf->Cell(50, $rowHeight, $row['judul'], 1, 0, 'L');
    
    // Cell untuk cover
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->Cell(40, $rowHeight, '', 1, 0, 'C'); // Kosongkan cell untuk gambar
    
    // Tambahkan gambar jika ada
    if(!empty($row['cover']) && file_exists($row['cover'])) {
        // Hitung rasio aspek dan ukuran gambar
        list($width, $height) = getimagesize($row['cover']);
        $ratio = $width / $height;
        $maxHeight = 28; // Tinggi maksimum gambar
        $maxWidth = 38; // Lebar maksimum gambar
        
        if($ratio > 1) {
            $newWidth = min($maxWidth, $width);
            $newHeight = $newWidth / $ratio;
        } else {
            $newHeight = min($maxHeight, $height);
            $newWidth = $newHeight * $ratio;
        }
        
        // Posisikan gambar di tengah cell
        $imageX = $x + (40 - $newWidth) / 2;
        $imageY = $y + ($rowHeight - $newHeight) / 2;
        $pdf->Image($row['cover'], $imageX, $imageY, $newWidth, $newHeight);
    } else {
        $pdf->SetXY($x, $y + $rowHeight/2 - 3);
        $pdf->Cell(40, 6, 'No Cover', 0, 0, 'C');
    }
    
    // Kembali ke posisi setelah cell gambar
    $pdf->SetXY($x + 40, $y);
    
    $pdf->Cell(50, $rowHeight, $row['pengarang'], 1, 0, 'L');
    $pdf->Cell(30, $rowHeight, $row['tahun_terbit'], 1, 1, 'C');
}

$pdf->Output();
?>
