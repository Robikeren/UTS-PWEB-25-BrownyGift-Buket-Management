<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fungsi untuk cek role tertentu
function checkRole($role) {
    if ($_SESSION['role'] != $role) {
        // Redirect ke login jika role salah
        session_destroy();
        header('Location: ../login.php');
        exit;
    }
}

// Fungsi untuk dapatkan info user
function getUserInfo() {
    global $conn;
    $id = $_SESSION['user_id'];
    $query = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    return mysqli_fetch_assoc($query);
}
?>