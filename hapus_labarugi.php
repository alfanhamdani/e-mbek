<?php
include 'koneksi.php';

// Pastikan parameter 'id_hasil_labarugi' ada
if (isset($_GET['id_hasil_labarugi'])) {
    $id_hasil_labarugi = intval($_GET['id_hasil_labarugi']); // Pastikan hanya angka untuk keamanan

    // Update nilai void menjadi 1
    $query = "UPDATE mbek_hasil_labarugi SET void = 1 WHERE id_hasil_labarugi = '$id_hasil_labarugi'";

    if (mysqli_query($conn, $query)) {
        header('Location: hasil_labarugi.php?status=success');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Parameter 'id_hasil_labarugi' tidak ditemukan!";
}
?>