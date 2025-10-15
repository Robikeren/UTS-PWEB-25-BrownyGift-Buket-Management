<?php
// admin/update_order.php
include '../config.php';
include 'auth.php';
checkRole('admin');

// Pastikan parameter ada
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header('Location: orders.php');
    exit;
}

$id = intval($_GET['id']); // Konversi ke integer untuk keamanan
$status = mysqli_real_escape_string($conn, $_GET['status']);

// Validasi status yang diizinkan
$allowed_status = ['processing', 'selesai'];
if (!in_array($status, $allowed_status)) {
    header('Location: orders.php');
    exit;
}

// Pastikan koneksi database ada
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek status saat ini
$check_query = mysqli_query($conn, "SELECT status FROM orders WHERE id = $id");
if (!$check_query) {
    die("Error memeriksa status: " . mysqli_error($conn));
}

if (mysqli_num_rows($check_query) == 0) {
    echo "<script>alert('Order dengan ID $id tidak ditemukan!'); window.location='orders.php';</script>";
    exit;
}

$current_status = mysqli_fetch_assoc($check_query)['status'];

// Logika status flow
if ($status == 'processing' && $current_status != 'pending') {
    echo "<script>alert('Order harus dalam status pending untuk diproses!'); window.location='orders.php';</script>";
    exit;
}

if ($status == 'selesai' && $current_status != 'processing') {
    echo "<script>alert('Order harus dalam status processing untuk diselesaikan!'); window.location='orders.php';</script>";
    exit;
}

// Update status dengan updated_at
$sql = "UPDATE orders SET status = '$status', updated_at = NOW() WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    $message = $status == 'processing' ? 'Pesanan mulai diproses!' : 'Pesanan selesai diproses!';
    echo "<script>
        alert('$message');
        window.location='orders.php';
    </script>";
} else {
    // Tampilkan error spesifik jika query gagal
    $error = mysqli_error($conn);
    echo "<script>alert('Gagal mengupdate status: $error'); window.location='orders.php';</script>";
}
?>