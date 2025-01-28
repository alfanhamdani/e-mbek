<?php
include 'koneksi.php';

// Ambil data dari database
$query = "SELECT * FROM mbek_hewan ORDER BY date_record DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pembelian Hewan</title>
    <link rel="stylesheet" href="assets/style.css"> <!-- CSS -->
</head>
<style>
    /* Global Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
    color: #333;
}

h1 {
    text-align: center;
    margin: 20px 0;
    color: #4CAF50;
}

/* Table Styles */
table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

table th, table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

table th {
    background-color: #4CAF50;
    color: white;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tr:hover {
    background-color: #ddd;
}

/* Button Styles */
a, button {
    display: inline-block;
    margin: 10px;
    padding: 10px 15px;
    font-size: 14px;
    text-decoration: none;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
}

a {
    background-color: #4CAF50;
    color: white;
}

a:hover {
    background-color: #45a049;
}

button {
    background-color: #4CAF50;
    color: white;
}

button:hover {
    background-color: #45a049;
}

a[href^="hapus_hewan.php"] {
    background-color: #f44336;
    color: white;
}

a[href^="hapus_hewan.php"]:hover {
    background-color: #e53935;
}

/* Form Styles */
form {
    width: 80%;
    max-width: 400px;
    margin: 20px auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

form label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}

form input, form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

form input:focus, form select:focus {
    border-color: #4CAF50;
    outline: none;
}

form button {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 5px;
}

form button:hover {
    background-color: #45a049;
}

form a {
    display: block;
    text-align: center;
    margin-top: 10px;
    color: #4CAF50;
}

form a:hover {
    text-decoration: underline;
}

</style>
<body>
    <h1>Daftar Pembelian Hewan</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Nama Hewan</th>
                <th>Jenis Kelamin</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_kelamin']) ?></td>
                    <td><?= htmlspecialchars($row['jumlah']) ?></td>
                    <td>Rp. <?= number_format($row['harga'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td>
                        <a href="edit_hewan.php?nama=<?= urlencode($row['nama']) ?>">Edit</a>
                        <a href="hapus_hewan.php?nama=<?= urlencode($row['nama']) ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="tambah_hewan.php">Tambah Data</a>
</body>
</html>