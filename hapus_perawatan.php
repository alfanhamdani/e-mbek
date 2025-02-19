<?php
include 'koneksi.php';

// Pastikan parameter 'id_perawatan' ada
if (isset($_GET['id_perawatan'])) {
    $id_perawatan = intval($_GET['id_perawatan']); // Pastikan hanya angka untuk keamanan

    // Perbaiki query dengan tanda kutip yang benar
    $query = "DELETE FROM mbek_perawatan WHERE id_perawatan = '$id_perawatan'";

    if (mysqli_query($conn, $query)) {
        header('Location: daftar_perawatan.php?status=success');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Parameter 'id_perawatan' tidak ditemukan!";
}
?>