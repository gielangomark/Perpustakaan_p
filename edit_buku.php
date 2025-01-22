<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

require 'koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id = $_GET['id'];
$buku = $conn->query("SELECT * FROM buku WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $cover = $buku['cover']; // Simpan cover lama sebagai default

    // Jika ada file baru yang diunggah
    if (isset($_FILES['cover']['name']) && $_FILES['cover']['name'] != "") {
        $targetDir = "uploads/";
        $fileName = uniqid() . basename($_FILES['cover']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validasi tipe file
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetFilePath)) {
                // Jika berhasil diunggah, ganti path cover
                $cover = $targetFilePath;
            } else {
                $error = "Gagal mengunggah file cover.";
            }
        } else {
            $error = "Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        }
    }

    // Update data buku
    $query = "UPDATE buku SET judul='$judul', pengarang='$pengarang', tahun_terbit='$tahun_terbit', cover='$cover' WHERE id=$id";
    if ($conn->query($query)) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Gagal mengupdate buku: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <link rel="stylesheet" href="assets\css/edit.css">
</head>
<body>
    <h1>Edit Buku</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <label>Judul Buku:</label><br>
        <input type="text" name="judul" value="<?= $buku['judul']; ?>" required><br>
        <label>Pengarang:</label><br>
        <input type="text" name="pengarang" value="<?= $buku['pengarang']; ?>" required><br>
        <label>Tahun Terbit:</label><br>
        <input type="number" name="tahun_terbit" value="<?= $buku['tahun_terbit']; ?>" required><br>
        <label>Cover Buku (Opsional):</label><br>
        <?php if (!empty($buku['cover'])): ?>
            <img src="<?= $buku['cover']; ?>" alt="Cover Buku" style="width: 100px; height: 150px;"><br>
        <?php endif; ?>
        <input type="file" name="cover"><br><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
