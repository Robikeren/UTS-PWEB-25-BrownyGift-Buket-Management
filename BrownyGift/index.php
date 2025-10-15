<?php
// index.php - File ROOT (ganti yang existing)
session_start();

// Jika sudah login, redirect berdasarkan role
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    switch($role) {
        case 'admin':
            header('Location: admin/');
            break;
        case 'customer':
            header('Location: customer/');
            break;
        case 'ekspedisi':
            header('Location: ekspedisi/');
            break;
        default:
            session_destroy();
            header('Location: login.php');
    }
} else {
    // Jika belum login, ke login page
    header('Location: login.php');
}
exit;
?>