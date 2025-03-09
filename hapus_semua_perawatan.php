<?php
require 'koneksi.php'; // Sesuaikan dengan koneksi database

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_hewan'])) {
    $id_hewan = $_POST['id_hewan'];

    $stmt = $conn->prepare("DELETE FROM mbek_perawatan WHERE id_hewan = ?");
    $stmt->bind_param("i", $id_hewan);

    if ($stmt->execute()) {
        echo "<script>window.location.href='daftar_perawatan.php?status=success';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Akses tidak valid!'); window.history.back();</script>";
}
?>