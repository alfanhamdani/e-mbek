<?php
include "koneksi.php";

// Validasi metode HTTP
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validasi dan sanitasi input
    if (!isset($_POST["jenis_pakan"]) || empty($_POST["jenis_pakan"])) {
        die("Jenis pakan tidak ditemukan.");
    }

    $jenis_pakan = $conn->real_escape_string($_POST["jenis_pakan"]);

    // Konfirmasi jika data ada
    $data = $conn->query("SELECT * FROM mbek_pakan WHERE jenis_pakan='$jenis_pakan'")->fetch_assoc();
    if (!$data) {
        die("Data pakan tidak ditemukan.");
    }

    // Hapus data
    $sql = "DELETE FROM mbek_pakan WHERE jenis_pakan='$jenis_pakan'";
    if ($conn->query($sql)) {
        header("Location: daftar_pakan.php?status=sukses_hapus");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    die("Metode HTTP tidak valid.");
}
?>
