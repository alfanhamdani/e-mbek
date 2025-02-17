<?php
include "koneksi.php";

// Validasi metode HTTP
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validasi dan sanitasi input
    if (!isset($_POST["id_pakan"]) || empty($_POST["id_pakan"])) {
        die("Jenis pakan tidak ditemukan.");
    }

    $id_pakan = $conn->real_escape_string($_POST["id_pakan"]);

    // Konfirmasi jika data ada
    $data = $conn->query("SELECT * FROM mbek_pakan WHERE id_pakan='$id_pakan'")->fetch_assoc();
    if (!$data) {
        die("Data pakan tidak ditemukan.");
    }

    // Hapus data
    $sql = "DELETE FROM mbek_pakan WHERE id_pakan='$id_pakan'";
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
