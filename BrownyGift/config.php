<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "brownygift";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// SET TIMEZONE KE WIB (UTC+7)
date_default_timezone_set('Asia/Jakarta');
mysqli_query($conn, "SET time_zone = '+07:00'");
mysqli_query($conn, "SET @@session.time_zone = '+07:00'");
?>