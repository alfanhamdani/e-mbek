<?php
include 'koneksi.php';

// Pastikan parameter 'id_pakan' ada
if (isset($_GET['id_pakan'])) {
    $id_pakan = intval($_GET['id_pakan']); // Pastikan hanya angka untuk keamanan

    // Perbaiki query dengan tanda kutip yang benar
    $query = "DELETE FROM mbek_pakan WHERE id_pakan = '$id_pakan'";

    if (mysqli_query($conn, $query)) {
        header('Location: daftar_pakan.php?status=success');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Parameter 'id_pakan' tidak ditemukan!";
}
?>