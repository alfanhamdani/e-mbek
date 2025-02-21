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
    <title>Detail Hewan</title>
    <link rel="stylesheet" href="w3.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="w3-container w3-padding-16">
        <h2 class="w3-center">Detail Hewan</h2>

        <a href="daftar_hewan.php" class="w3-button w3-gray w3-margin-bottom"><i class="fa fa-arrow-left"></i> Kembali</a>

        <div class="w3-card w3-padding w3-light-grey">
            <p><b>ID Hewan:</b> <?= $data['id_hewan']; ?></p>
            <p><b>Jenis Kelamin:</b> <?= $data['jenis_kelamin']; ?></p>
            <p><b>Jumlah:</b> <?= $data['jumlah']; ?></p>
            <p><b>Harga:</b> Rp <?= number_format($data['harga'], 2, ',', '.'); ?></p>
            <p><b>Tanggal:</b> <?= $data['tanggal']; ?></p>
            <p><b>Dicatat oleh:</b> <?= $data['user_record']; ?></p>
            <p><b>Terakhir diubah oleh:</b> <?= $data['user_modified'] ?? '-'; ?></p>
            <p><b>QR Link:</b> 
                <?php if ($data['qr_link']) { ?>
                    <a href="<?= $data['qr_link']; ?>" target="_blank" class="w3-button w3-blue">
                        <i class="fa fa-qrcode"></i> Lihat QR
                    </a>
                <?php } else { ?>
                    <span class="w3-text-red">Belum Ada</span>
                <?php } ?>
            </p>
        </div>
    </div>
</body>

</html>
