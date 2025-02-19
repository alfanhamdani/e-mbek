<?php
include 'koneksi.php';

// Pastikan parameter 'id_hewan' ada
if (isset($_GET['id_hewan'])) {
    $id_hewan = intval($_GET['id_hewan']); // Pastikan hanya angka untuk keamanan

    // Update nilai void menjadi 1
    $query = "UPDATE mbek_hewan SET void = 1 WHERE id_hewan = '$id_hewan'";

    if (mysqli_query($conn, $query)) {
        header('Location: daftar_hewan.php?status=success');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Parameter 'id_hewan' tidak ditemukan!";
}
?>