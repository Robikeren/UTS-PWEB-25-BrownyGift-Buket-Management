<?php
// admin/index.php
include '../config.php';
include 'auth.php';
checkRole('admin');

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$search_sql = $keyword ? 
    "WHERE nama_buket LIKE '%$keyword%' OR jenis_bunga LIKE '%$keyword%' OR id LIKE '%$keyword%'" : 
    "";

$sql = "SELECT * FROM buket $search_sql ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - BrownyGift</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>🌸 Admin Dashboard - BrownyGift 🌸</h1>
        <div class="actions">
            <a href="add.php" class="btn">+ Tambah Buket</a>
            <a href="orders.php" class="btn">📋 Manage Pesanan</a>
            <a href="../logout.php" class="btn delete">Logout</a>
            <input type="text" id="search" placeholder="Cari buket..." autocomplete="off">
        </div>

        <div id="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Buket</th>
                    <th>Jenis Item</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Terakhir Diperbarui</th>
                    <th>Aksi</th>
                </tr>

                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td>
                        <?php if (!empty($row['gambar'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="Gambar Buket" width="100" height="100" style="object-fit: cover;">
                        <?php else: ?>
                            Tidak ada gambar
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['nama_buket']); ?></td>
                    <td><?= htmlspecialchars($row['jenis_bunga']); ?></td>
                    <td><?= htmlspecialchars($row['stok']); ?></td>
                    <td>Rp<?= number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td>
                    <?php 
                    if (!empty($row['tanggal_update'])) {
                        echo date('d/m/Y H:i', strtotime($row['tanggal_update']));
                    } else {
                        echo '-';
                    }
                    ?></td>
                    <td class="action-buttons">
                        <a href="edit.php?id=<?= $row['id']; ?>" class="btn">Edit</a>
                        <a href="delete.php?id=<?= $row['id']; ?>" 
                           class="btn delete" 
                           onclick="return confirmDelete('<?= $row['nama_buket']; ?>')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>