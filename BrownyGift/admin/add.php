<?php
// admin/add.php
include '../config.php';  // ← Path ke root
include 'auth.php';      // ← Auth
checkRole('admin');

if (isset($_POST['submit'])) {
    $nama_buket = mysqli_real_escape_string($conn, $_POST['nama_buket']);
    $jenis_bunga = mysqli_real_escape_string($conn, $_POST['jenis_bunga']);
    $stok = intval($_POST['stok']);
    $harga = floatval($_POST['harga']);
    $tanggal_masuk = date('Y-m-d');
    $tanggal_update = date('Y-m-d H:i:s');
    $gambar = '';

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/";  // ← Path ke root
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
        
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
    }

    $sql = "INSERT INTO buket (nama_buket, jenis_bunga, stok, harga, tanggal_masuk, tanggal_update, gambar) 
            VALUES ('$nama_buket', '$jenis_bunga', '$stok', '$harga', '$tanggal_masuk', '$tanggal_update', '$gambar')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Berhasil ditambah!'); window.location='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Buket - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Tambah Data Buket</h1>
        <a href="index.php" class="btn">← Kembali</a>
        <form method="POST" enctype="multipart/form-data">
            <label>Nama Buket:</label>
            <input type="text" name="nama_buket" required>

            <label>Jenis Item:</label>
            <input type="text" name="jenis_bunga" required>

            <label>Stok:</label>
            <input type="number" name="stok" min="1" required>

            <label>Harga:</label>
            <input type="number" name="harga" min="0" required>

            <label>Gambar:</label>
            <input type="file" name="gambar" accept="image/*">

            <div class="actions">
                <button type="submit" name="submit" class="btn">Simpan</button>
            </div>
        </form>
    </div>
</body>
</html>