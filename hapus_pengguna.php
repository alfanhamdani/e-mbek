<?php
include 'koneksi.php';

session_start(); // Mulai sesi untuk mengakses informasi sesi pengguna

// Pastikan pengguna telah login sebelumnya
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect jika pengguna belum login
    exit;
}

// Ambil aksi dari URL atau form
$action = $_GET['action'] ?? '';

if ($action === 'delete' && isset($_GET['username'])) {
    $username = $_GET['username'];
    $username = mysqli_real_escape_string($conn, $username); // Sanitasi input

    $sql = "DELETE FROM mbek_pengguna WHERE username='$username'";

    if (mysqli_query($conn, $sql)) {
        header('Location: daftar_pengguna.php');
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>