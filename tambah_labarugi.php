<?php
include 'koneksi.php';
session_start();

// Pastikan pengguna telah login sebelumnya
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect jika pengguna belum login
    exit;
}

$username = $_SESSION['username']; // Ambil username pengguna dari sesi

// Proses simpan data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_hewan = intval($_POST['id_hewan']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jumlah = intval($_POST['jumlah']);
    $hpp = floatval($_POST['hpp']);
    $harga = floatval($_POST['harga']);
    $tanggal_pembelian = $_POST['tanggal_pembelian'];
    $tanggal_penjualan = $_POST['tanggal_penjualan'];
    $total_keuntungan = $harga > $hpp ? $harga - $hpp : 0;
    $total_kerugian = $hpp > $harga ? $hpp - $harga : 0;
    $date_record = date('Y-m-d H:i:s'); // Waktu saat ini
    $user_record = $username;
    $void = 0;

    // Simpan data ke tabel lain (contoh: tabel mbek_hasil_labarugi)
    $sql = "INSERT INTO mbek_hasil_labarugi (
                id_hewan, jenis_kelamin, jumlah, hpp, harga, 
                tanggal_pembelian, tanggal_penjualan, total_keuntungan, 
                total_kerugian, date_record, 
                user_record, void
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isiddsssddsi",
        $id_hewan,
        $jenis_kelamin,
        $jumlah,
        $hpp,
        $harga,
        $tanggal_pembelian,
        $tanggal_penjualan,
        $total_keuntungan,
        $total_kerugian,
        $date_record,
        $user_record,
        $void
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan!');</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

// Jika ada permintaan data hewan (AJAX)
if (isset($_GET['id_hewan'])) {
    $id_hewan = intval($_GET['id_hewan']); // Pastikan input aman

    // Query mengambil data dari 3 tabel menggunakan LEFT JOIN
    $sql = "SELECT 
                h.jenis_kelamin, 
                h.jumlah, 
                h.harga AS hpp, 
                h.tanggal,
                COALESCE(SUM(p.harga_pakan), 0) AS harga_pakan, 
                COALESCE(SUM(pr.harga_perawatan), 0) AS harga_perawatan
            FROM mbek_hewan h
            LEFT JOIN mbek_pakan p ON h.id_hewan = p.id_hewan
            LEFT JOIN mbek_perawatan pr ON h.id_hewan = pr.id_hewan
            WHERE h.id_hewan = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_hewan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data); // Kirim data dalam format JSON
    } else {
        echo json_encode(null); // Data tidak ditemukan
    }

    $stmt->close();
    $conn->close();
    exit(); // Hentikan eksekusi script setelah merespons AJAX
}

// Ambil daftar hewan untuk dropdown
$sql = "SELECT id_hewan FROM mbek_hewan";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="w3.css">
    <link rel="icon" href="logo e-mbek.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Tambah Laba Rugi</title>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('id_hewan').addEventListener('change', function () {
                const idHewan = this.value;

                if (idHewan) {
                    fetch(`tambah_labarugi.php?id_hewan=${idHewan}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
                                document.getElementById('jumlah').value = data.jumlah;
                                document.getElementById('hpp').value = data.hpp; // Harga jadi HPP
                                document.getElementById('tanggal_pembelian').value = data.tanggal;
                                document.getElementById('harga_pakan').value = data.harga_pakan;
                                document.getElementById('harga_perawatan').value = data.harga_perawatan;
                            } else {
                                alert('Data tidak ditemukan untuk ID Hewan ini.');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            document.getElementById('harga').addEventListener('input', function () {
                const hpp = parseFloat(document.getElementById('hpp').value) || 0;
                const hargaPenjualan = parseFloat(this.value) || 0;
                const hargaPakan = parseFloat(document.getElementById('harga_pakan').value) || 0;
                const hargaPerawatan = parseFloat(document.getElementById('harga_perawatan').value) || 0;

                const totalBiaya = hpp + hargaPakan + hargaPerawatan;
                const totalKeuntungan = hargaPenjualan - totalBiaya;
                const totalKerugian = totalBiaya > hargaPenjualan ? totalBiaya - hargaPenjualan : 0;

                document.getElementById('total_keuntungan').value = totalKeuntungan > 0 ? totalKeuntungan.toFixed(2) : 0;
                document.getElementById('total_kerugian').value = totalKerugian > 0 ? totalKerugian.toFixed(2) : 0;
            });
        });
    </script>

</head>

<body>
    <h1>Form Data Hewan</h1>
    <form action="tambah_labarugi.php" method="POST">
        <label for="id_hewan">ID Kambing</label>
        <select name="id_hewan" id="id_hewan" required>
            <option value="">Pilih id kambing</option>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id_hewan']}'>{$row['id_hewan']}</option>";
                }
            } else {
                echo "<option value=''>Data Hewan Tidak Ada</option>";
            }
            ?>
        </select><br><br>

        <label for="jenis_kelamin">Jenis Kelamin</label>
        <input type="text" name="jenis_kelamin" id="jenis_kelamin" readonly><br><br>

        <label for="jumlah">Jumlah</label>
        <input type="number" name="jumlah" id="jumlah" readonly><br><br>

        <label for="hpp">HPP (Harga Pokok Pembelian)</label>
        <input type="number" name="hpp" id="hpp" step="0.01" readonly><br><br>

        <label for="harga">Harga</label>
        <input type="number" name="harga" id="harga" step="0.01" required><br><br>

        <label for="tanggal_pembelian">Tanggal Pembelian</label>
        <input type="datetime-local" name="tanggal_pembelian" id="tanggal_pembelian" readonly><br><br>

        <label for="tanggal_penjualan">Tanggal Penjualan</label>
        <input type="datetime-local" name="tanggal_penjualan" id="tanggal_penjualan"><br><br>

        <label for="harga_pakan">Total Pakan</label>
        <input type="number" name="harga_pakan" id="harga_pakan" step="0.01" readonly><br><br>

        <label for="harga_perawatan">Total Perawatan</label>
        <input type="number" name="harga_perawatan" id="harga_perawatan" step="0.01" readonly><br><br>

        <label for="total_keuntungan">Total Keuntungan</label>
        <input type="number" name="total_keuntungan" id="total_keuntungan" step="0.01" readonly><br><br>

        <label for="total_kerugian">Total Kerugian</label>
        <input type="number" name="total_kerugian" id="total_kerugian" step="0.01" readonly><br><br>

        <button type="submit">Simpan Data</button>
    </form>
</body>

</html>