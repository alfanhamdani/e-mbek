<?php
include "koneksi.php";

if (!isset($_GET["jenis_pakan"])) {
    die("Jenis perawatan tidak ditemukan.");
}
$jenis_pakan = $conn->real_escape_string($_GET["jenis_pakan"]);
$data = $conn->query("SELECT * FROM mbek_pakan WHERE jenis_pakan='$jenis_pakan'")->fetch_assoc();

if (!$data) {
    die("Data perawatan tidak ditemukan.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $jenis_pakan = $conn->real_escape_string($_POST["jenis_pakan"]);
    $harga = $conn->real_escape_string($_POST["harga"]);
    $tanggal = $conn->real_escape_string($_POST["tanggal"]);
    $user_modified = "admin"; // Ganti dengan user login sebenarnya

    $sql = "UPDATE mbek_pakan SET 
            harga='$harga', tanggal='$tanggal', date_modified=NOW(), user_modified='$user_modified' 
            WHERE jenis_pakan='$jenis_pakan'";

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
    <title>Edit Perawatan</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="w3-container">
        <h2>Edit Perawatan</h2>
        <form method="POST" action="">
            <label>Jenis Perawatan</label>
            <input class="w3-input" type="text" name="jenis_pakan" value="<?php echo htmlspecialchars($data['jenis_pakan']); ?>" readonly>
            <label>Harga</label>
            <input class="w3-input" type="number" name="harga" value="<?php echo htmlspecialchars($data['harga']); ?>" required>
            <label>Tanggal</label>
            <input class="w3-input" type="date" name="tanggal" value="<?php echo htmlspecialchars($data['tanggal']); ?>" required>
            <button class="w3-button w3-blue w3-margin-top">Simpan</button>
            <a href="daftar_perawatan.php" class="w3-button w3-grey w3-margin-top">Kembali</a>
        </form>
    </div>
</body>
</html>
