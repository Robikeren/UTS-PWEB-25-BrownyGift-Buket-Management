<?php
// ekspedisi/update.php
include '../config.php';
include '../auth.php';
checkRole('ekspedisi');

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);
$status = mysqli_real_escape_string($conn, $_GET['status']);

// Validasi - hanya bisa update ke 'dikirim' dari 'selesai'
if ($status != 'dikirim') {
    header('Location: index.php');
    exit;
}

// Cek order valid dan status saat ini
$check_query = mysqli_query($conn, "SELECT status FROM orders WHERE id = $id");
if (!$check_query || mysqli_num_rows($check_query) == 0) {
    echo "<script>alert('Order tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

$current_status = mysqli_fetch_assoc($check_query)['status'];

if ($current_status != 'selesai') {
    echo "<script>alert('Order harus dalam status selesai untuk dikirim!'); window.location='index.php';</script>";
    exit;
}

// Update status
$sql = "UPDATE orders SET status='dikirim', updated_at=NOW() WHERE id=$id";
if (mysqli_query($conn, $sql)) {
    echo "<script>
        alert('✅ Pesanan #{$id} berhasil ditandai sebagai dikirim!');
        window.location='index.php';
    </script>";
} else {
    echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location='index.php';</script>";
}
?>