<?php
include 'koneksi.php';

$nama = $_GET['nama'];
$query = "DELETE FROM mbek_hewan WHERE nama = $nama";

if (mysqli_query($conn, $query)) {
    header('Location: daftar_hewan.php');
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
