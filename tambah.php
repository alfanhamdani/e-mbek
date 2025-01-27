<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $tanggal = $_POST['tanggal'];
    $user_record = 'admin'; // Sesuaikan dengan pengguna yang login
    $date_record = date('Y-m-d H:i:s');

    // Query insert data
    $query = "INSERT INTO mbek_hewan (nama, jenis_kelamin, jumlah, harga, tanggal, date_record, user_record) 
              VALUES ('$nama', '$jenis_kelamin', $jumlah, $harga, '$tanggal', '$date_record', '$user_record')";
    if (mysqli_query($conn, $query)) {
        header('Location: index.php');  
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pembelian Hewan</title>
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

a[href^="hapus.php"] {
    background-color: #f44336;
    color: white;
}

a[href^="hapus.php"]:hover {
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
    <h1>Tambah Pembelian Hewan</h1>
    <form method="POST" action="">
        <label>Nama Hewan: <input type="text" name="nama" required></label><br>
        <label>Jenis Kelamin: 
            <select name="jenis_kelamin" required>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </label><br>
        <label>Jumlah: <input type="number" name="jumlah" required></label><br>
        <label>Harga: <input type="number" name="harga" required></label><br>
        <label>Tanggal: <input type="date" name="tanggal" required></label><br>
        <button type="submit">Tambah</button>
        <a href="index.php">Kembali</a>
    </form>
</body>
</html>
