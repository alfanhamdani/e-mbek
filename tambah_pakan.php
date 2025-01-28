<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis_pakan = $_POST["jenis_pakan"];
    $berat = $_POST["berat"];
    $harga = $_POST["harga"];
    $tanggal = $_POST["tanggal"];
    $user_record = "admin";

    $sql = "INSERT INTO mbek_pakan (jenis_pakan, berat, harga, tanggal, date_record, user_record) 
            VALUES ('$jenis_pakan', $berat, $harga, '$tanggal', NOW(), '$user_record')";
    if ($conn->query($sql)) {
        header("Location: daftar_pakan.php");
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
    <title>Tambah Pakan</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="w3-container">
        <h2>Tambah Pakan</h2>
        <form class="w3-container" method="POST">
            <p>
                <label>Jenis Pakan</label>
                <input class="w3-input" type="text" name="jenis_pakan" required>
            </p>
            <p>
                <label>Berat (kg)</label>
                <input class="w3-input" type="number" name="berat" required>
            </p>
            <p>
                <label>Harga</label>
                <input class="w3-input" type="number" step="0.01" name="harga" required>
            </p>
            <p>
                <label>Tanggal</label>
                <input class="w3-input" type="date" name="tanggal" required>
            </p>
            <button class="w3-button w3-green">Tambah</button>
            <a href="daftar_pakan.php" class="w3-button w3-grey">Kembali</a>
        </form>
    </div>
</body>
</html>
