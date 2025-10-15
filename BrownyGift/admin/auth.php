<?php
// admin/auth.php - Copy dari root auth.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

function checkRole($role) {
    if ($_SESSION['role'] != $role) {
        session_destroy();
        header('Location: ../login.php');
        exit;
    }
}

function getUserInfo() {
    global $conn;
    $id = $_SESSION['user_id'];
    $query = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
    return mysqli_fetch_assoc($query);
}
?>