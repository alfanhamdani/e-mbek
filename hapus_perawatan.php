<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["jenis_pakan"]) || empty($_POST["jenis_pakan"])) {
        die("Jenis perawatan tidak ditemukan.");
    }
    $jenis_pakan = $conn->real_escape_string($_POST["jenis_pakan"]);

    $sql = "DELETE FROM mbek_pakan WHERE jenis_pakan='$jenis_pakan'";
    if ($conn->query($sql)) {
        header("Location: daftar_perawatan.php?status=sukses_hapus");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
