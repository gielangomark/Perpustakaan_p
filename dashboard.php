<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

require 'koneksi.php';

// Inisialisasi variabel pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mengambil data dengan pencarian
$query = "SELECT * FROM buku WHERE judul LIKE '%$search%'";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<header>
<h1>Dashboard Admin</h1>
</header>

<body>
    <div class="container">
        <div class="nav-links">
            <a href="tambah_buku.php" class="btn">Tambah Buku</a>
            <a href="logout.php" class="btn logout">Logout</a>
        </div>

        <!-- Form pencarian dengan styling -->
       <form method="GET" action="" class="search-container">
    <input type="text" name="search" placeholder="Cari judul..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Cover</th>
                        <th>Pengarang</th>
                        <th>Tahun Terbit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $id = 1;
                    while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $id++; ?></td>
                        <td><?= $row['judul'] ?></td>
                        <td>
                            <?php if (!empty($row['cover'])): ?>
                                <img src="<?= $row['cover'] ?>" alt="Cover Buku">
                            <?php else: ?>
                                <p>Tidak ada cover</p>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['pengarang'] ?></td>
                        <td><?= $row['tahun_terbit'] ?></td>
                        <td>
                            <a class="btn-edit" href="edit_buku.php?id=<?= $row['id'] ?>">Edit</a>
                            <a class="btn-delete" href="hapus_buku.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus buku ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="laporan_buku.php" target="_blank" class="btn-report">Cetak Laporan Buku Hari Ini</a>
    </div>
</body>
</html>