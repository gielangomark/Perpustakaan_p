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

    // Handle file upload
    $cover = $_FILES['cover']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($cover);
    $upload_ok = 1;
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an actual image
    $check = getimagesize($_FILES['cover']['tmp_name']);
    if ($check === false) {
        echo "File is not an image.";
        $upload_ok = 0;
    }

    // Allow certain file formats
    if (!in_array($image_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $upload_ok = 0;
    }

    // Check if $upload_ok is set to 0 by an error
    if ($upload_ok == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_file)) {
            // Insert data into database
            $stmt = $conn->prepare("INSERT INTO buku (judul, pengarang, tahun_terbit, cover) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $judul, $pengarang, $tahun_terbit, $cover);

            if ($stmt->execute()) {
                echo "Buku berhasil ditambahkan.";
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
</head>
<body>
    <h1>Tambah Buku</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Judul:</label><br>
        <input type="text" name="judul" required><br>

        <label>Pengarang:</label><br>
        <input type="text" name="pengarang" required><br>

        <label>Tahun Terbit:</label><br>
        <input type="number" name="tahun_terbit" required><br>

        <label>Cover Buku:</label><br>
        <input type="file" name="cover" accept="image/*" required><br><br>

        <button type="submit">Tambah Buku</button>
    </form>
</body>
</html>
