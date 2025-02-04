<?php
include "koneksi.php";

// Validasi dan sanitasi input
if (!isset($_GET["jenis_pakan"])) {
    die("Jenis pakan tidak ditemukan.");
}
$jenis_pakan = $conn->real_escape_string($_GET["jenis_pakan"]);

// Ambil data dari database
$data = $conn->query("SELECT * FROM mbek_pakan WHERE jenis_pakan='$jenis_pakan'")->fetch_assoc();
if (!$data) {
    die("Data pakan tidak ditemukan.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis_pakan = $conn->real_escape_string($_POST["jenis_pakan"]);
    $berat = intval($_POST["berat"]);
    $harga = floatval($_POST["harga"]);
    $tanggal = $conn->real_escape_string($_POST["tanggal"]);
    $user_modified = "admin";

    // Update data ke database
    $sql = "UPDATE mbek_pakan SET 
            jenis_pakan='$jenis_pakan', berat=$berat, harga=$harga, tanggal='$tanggal', 
            date_modified=NOW(), user_modified='$user_modified' 
            WHERE jenis_pakan='$jenis_pakan'";
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
    <title>Edit Pakan</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="w3-container">
        <h2>Edit Pakan</h2>
        <form class="w3-container" method="POST">
            <p>
                <label>Jenis Pakan</label>
                <input class="w3-input" type="text" name="jenis_pakan" value="<?php echo $data['jenis_pakan']; ?>" required>
            </p>
            <p>
                <label>Berat (kg)</label>
                <input class="w3-input" type="number" name="berat" value="<?php echo $data['berat']; ?>" required>
            </p>
            <p>
                <label>Harga</label>
                <input class="w3-input" type="number" step="0.01" name="harga" value="<?php echo $data['harga']; ?>" required>
            </p>
            <p>
                <label>Tanggal</label>
                <input class="w3-input" type="date" name="tanggal" value="<?php echo $data['tanggal']; ?>" required>
            </p>
            <button class="w3-button w3-blue">Simpan</button>
            <a href="daftar_pakan.php" class="w3-button w3-grey">Kembali</a>
        </form>
    </div>
</body>
</html>
