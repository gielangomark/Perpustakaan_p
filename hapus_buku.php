<?php
require 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id = $_GET['id'];
$query = "DELETE FROM buku WHERE id=$id";
if ($conn->query($query)) {
    header('Location: dashboard.php');
    exit();
} else {
    echo "Gagal menghapus buku: " . $conn->error;
}
?>
