<?php
// admin/delete.php
include '../config.php';
include 'auth.php';
checkRole('admin');

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT gambar FROM buket WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

$delete = "DELETE FROM buket WHERE id = '$id'";
if (mysqli_query($conn, $delete)) {
    if (!empty($data['gambar']) && file_exists("../uploads/" . $data['gambar'])) {
        unlink("../uploads/" . $data['gambar']);
    }
    echo "<script>alert('Berhasil dihapus!'); window.location='index.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>