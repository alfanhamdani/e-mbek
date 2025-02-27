<?php
include 'koneksi.php';
session_start();

// Pastikan pengguna telah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Pastikan ID hewan ada di URL
if (!isset($_GET['id_hewan']) || empty($_GET['id_hewan'])) {
    echo "ID Hewan tidak ditemukan!";
    exit;
}

$id_hewan = intval($_GET['id_hewan']); // Pastikan ID adalah angka

// Query untuk mengambil data hewan berdasarkan ID
$query = "SELECT * FROM mbek_hewan WHERE id_hewan = $id_hewan LIMIT 1";
$result = mysqli_query($conn, $query);

// Periksa apakah data ditemukan
if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data tidak ditemukan!";
    exit;
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Hewan</title>
    <link rel="stylesheet" href="w3.css">
    <link rel="icon" href="logo e-mbek.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="w3-light-grey">


    <!-- Page Content -->
    <div id="mainContent" style="margin-left: 0; transition: margin-left 0.5s;">
        <div class="w3-green" style="display: flex; align-items: center; padding: 10px;">
            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
                <h3
                    style="margin: 0; line-height: 1.5rem; text-align: center; font-size: 25px; margin-top:5px; margin-bottom: 10px;">
                    <b>Hasil Pindai</b>
                </h3>
            </div>
        </div>


        <div class="w3-card w3-padding w3-light-grey">
            <p><b>ID Hewan:</b> <?= $data['id_hewan']; ?></p>
            <p><b>Jenis Kelamin:</b> <?= $data['jenis_kelamin']; ?></p>
            <p><b>Jumlah:</b> <?= $data['jumlah']; ?></p>
            <p><b>Harga:</b> Rp. <?= number_format($data['harga'], 0, ',', '.'); ?></p>
            <p><b>Tanggal:</b> <?= $data['tanggal']; ?></p>
            <p><b>Dicatat oleh:</b> <?= $data['user_record']; ?></p>
            <p><b>Terakhir diubah oleh:</b> <?= $data['user_modified'] ?? '-'; ?></p>
            <p><b>Gambar: </b><img src="<?= htmlspecialchars($data['gambar']); ?>" alt="Gambar Hewan"
                    style="width: 100px; height: auto;"></p><br>

            <a href="scan_code.php" class="w3-button w3-blue w3-margin-bottom"><i class="fa fa-arrow-left"></i>
                Kembali</a>
        </div>
    </div>

</body>

</html>