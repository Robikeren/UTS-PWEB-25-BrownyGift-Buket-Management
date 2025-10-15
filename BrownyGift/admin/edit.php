<?php
// admin/edit.php
include '../config.php';
include 'auth.php';
checkRole('admin');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM buket WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $nama_buket = mysqli_real_escape_string($conn, $_POST['nama_buket']);
    $jenis_bunga = mysqli_real_escape_string($conn, $_POST['jenis_bunga']);
    $stok = intval($_POST['stok']);
    $harga = floatval($_POST['harga']);
    $tanggal_update = date('Y-m-d H:i:s');
    $gambar = $data['gambar'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $gambar = time() . '_' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $gambar;
        
        $max_size = 5 * 1024 * 1024;
        if ($_FILES['gambar']['size'] > $max_size) {
            echo "<script>alert('File terlalu besar!'); window.history.back();</script>";
            exit;
        }
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            if (!empty($data['gambar']) && file_exists("../uploads/" . $data['gambar'])) {
                unlink("../uploads/" . $data['gambar']);
            }
        }
    }

    $sql = "UPDATE buket SET 
                nama_buket = '$nama_buket', 
                jenis_bunga = '$jenis_bunga',
                stok = '$stok', 
                harga = '$harga',
                tanggal_update = '$tanggal_update',
                gambar = '$gambar'
            WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Berhasil diupdate!'); window.location='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buket - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Data Buket</h1>
        <a href="index.php" class="btn">← Kembali</a>
        <form method="POST" enctype="multipart/form-data">
            <label>ID Buket:</label>
            <input type="text" value="<?= htmlspecialchars($data['id']); ?>" disabled>

            <label>Nama Buket:</label>
            <input type="text" name="nama_buket" value="<?= htmlspecialchars($data['nama_buket']); ?>" required>

            <label>Jenis Item:</label>
            <input type="text" name="jenis_bunga" value="<?= htmlspecialchars($data['jenis_bunga']); ?>" required>

            <label>Stok:</label>
            <input type="number" name="stok" value="<?= htmlspecialchars($data['stok']); ?>" min="1" required>

            <label>Harga:</label>
            <input type="number" name="harga" value="<?= htmlspecialchars($data['harga']); ?>" min="0" required>

            <label>Gambar Saat Ini:</label>
            <?php if (!empty($data['gambar'])): ?>
                <img src="../uploads/<?= htmlspecialchars($data['gambar']); ?>" alt="Gambar" width="100" height="100">
            <?php endif; ?>

            <label>Ganti Gambar:</label>
            <input type="file" name="gambar" accept="image/*">

            <div class="actions">
                <button type="submit" name="update" class="btn">Update</button>
            </div>
        </form>
    </div>
</body>
</html>