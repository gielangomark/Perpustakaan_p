<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun_terbit = $_POST['tahun_terbit'];

    // Variabel untuk menyimpan nama file cover
    $cover = null;

    // Proses upload file cover
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Buat folder jika belum ada
            }
            $upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true); // Membuat folder jika belum ada
}
            $cover = $upload_dir . uniqid() . '.' . $file_extension;
            move_uploaded_file($_FILES['cover']['tmp_name'], $cover);
        } else {
            $error = "Hanya file gambar yang diperbolehkan (JPG, JPEG, PNG, GIF).";
        }
    }

    if (!isset($error)) {
        // Masukkan data ke database
        $query = "INSERT INTO buku (judul, pengarang, tahun_terbit, cover) VALUES ('$judul', '$pengarang', '$tahun_terbit', '$cover')";
        if ($conn->query($query)) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Gagal menambahkan buku: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
    <link rel="stylesheet" href="assets\css/tambah.css">
</head>
<body>
    <h1>Tambah Buku</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <label>Judul Buku:</label><br>
        <input type="text" name="judul" required><br>
        <label>Pengarang:</label><br>
        <input type="text" name="pengarang" required><br>
        <label>Tahun Terbit:</label><br>
        <input type="number" name="tahun_terbit" required><br>
        <label>Cover Buku:</label><br>
        <input type="file" name="cover" accept="image/*"><br>
        <button type="submit">Tambah</button>
    </form>
</body>
</html>
