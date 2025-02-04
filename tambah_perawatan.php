<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jenis_pakan = $conn->real_escape_string($_POST["jenis_pakan"]);
    $harga = $conn->real_escape_string($_POST["harga"]);
    $tanggal = $conn->real_escape_string($_POST["tanggal"]);
    $user_record = "admin"; // Ganti dengan user login sebenarnya

    $sql = "INSERT INTO mbek_pakan (jenis_pakan, harga, tanggal, date_record, user_record)
            VALUES ('$jenis_pakan', '$harga', '$tanggal', NOW(), '$user_record')";

    if ($conn->query($sql)) {
        header("Location: daftar_perawatan.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Perawatan</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="w3-container">
        <h2>Tambah Perawatan</h2>
        <form method="POST" action="">
            <label>Jenis Perawatan</label>
            <input class="w3-input" type="text" name="jenis_pakan" required>
            <label>Harga</label>
            <input class="w3-input" type="number" name="harga" required>
            <label>Tanggal</label>
            <input class="w3-input" type="date" name="tanggal" required>
            <button class="w3-button w3-green w3-margin-top">Tambah</button>
            <a href="daftar_perawatan.php" class="w3-button w3-grey w3-margin-top">Kembali</a>
        </form>
    </div>
</body>
</html>
