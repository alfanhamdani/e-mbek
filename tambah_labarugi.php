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
    $jumlahData = count($_POST['id_hewan']);

    for ($i = 0; $i < $jumlahData; $i++) {
        $id_hewan = intval($_POST['id_hewan'][$i]);
        $jenis_kelamin = $_POST['jenis_kelamin'][$i];
        $jumlah = intval($_POST['jumlah'][$i]);
        $hpp = floatval($_POST['hpp'][$i]);
        $harga = floatval($_POST['harga'][$i]);
        $tanggal_pembelian = $_POST['tanggal_pembelian'][$i];
        $total_pakan = floatval($_POST['total_pakan'][$i]);
        $total_perawatan = floatval($_POST['total_perawatan'][$i]);
        $total_keuntungan = floatval($_POST['total_keuntungan'][$i]);
        $total_kerugian = floatval($_POST['total_kerugian'][$i]);
        $tanggal_penjualan = date('Y-m-d'); // Atau dari input jika ingin
        $date_record = date('Y-m-d H:i:s');
        $user_record = $username;
        $void = 0;

        $query = "INSERT INTO mbek_hasil_labarugi (id_hewan, jenis_kelamin, jumlah, hpp, harga, tanggal_pembelian, tanggal_penjualan, total_pakan, total_perawatan, total_keuntungan, total_kerugian, date_record, user_record, void) 
                  VALUES ('$id_hewan', '$jenis_kelamin', '$jumlah', '$hpp', '$harga', '$tanggal_pembelian', '$tanggal_penjualan', '$total_pakan', '$total_perawatan', '$total_keuntungan', '$total_kerugian', '$date_record', '$user_record', '$void')";

        mysqli_query($conn, $query);
    }

    header('Location: hasil_labarugi.php');
    exit;
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
    COALESCE(SUM(p.harga_pakan), 0) AS total_pakan, 
    (SELECT COALESCE(SUM(pr.harga_perawatan), 0) FROM mbek_perawatan pr WHERE pr.id_hewan = h.id_hewan) AS total_perawatan
    FROM mbek_hewan h
    LEFT JOIN mbek_pakan p ON h.id_hewan = p.id_hewan
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

// Ambil daftar hewan untuk dropdown, hanya yang memiliki void = 0
$sql = "SELECT mh.id_hewan 
        FROM mbek_hewan mh
        LEFT JOIN mbek_hasil_labarugi hl ON mh.id_hewan = hl.id_hewan
        WHERE mh.void = 0 AND hl.id_hewan IS NULL";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="w3.css">
    <link rel="icon" href="logo e-mbek.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Tambah Laba Rugi</title>
    <style>
        .w3-sidebar {
            z-index: 1100;
            position: fixed;
            left: -250px;
            width: 250px;
            height: 100%;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 0;
            background-color: #f4f4f4;
            border-right: 1px solid #ccc;
        }

        .w3-sidebar.show {
            left: 0;
        }

        .w3-sidebar a {
            padding: 10px;
            text-decoration: none;
            font-size: 18px;
            color: black;
            display: block;
        }

        .w3-sidebar a:hover {
            background-color: #ddd;
        }

        .w3-sidebar .close-button {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 15px;
            background-color: #f44336;
            color: white;
            font-size: 20px;
            text-align: center;
            border: none;
            cursor: pointer;
        }

        .w3-sidebar-overlay {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .w3-sidebar-overlay.show {
            display: block;
        }
    </style>


    <script>
        function tambahBaris() {
            const formRow = document.querySelector('.form-row');
            const clone = formRow.cloneNode(true);

            // Kosongkan input pada baris baru
            clone.querySelectorAll('input, select').forEach(el => {
                if (el.type !== 'button') el.value = '';
            });

            document.getElementById('form-container').appendChild(clone);
            bindEvents(); // re-bind event listener
        }

        function hapusBaris(button) {
            const rows = document.querySelectorAll('.form-row');
            if (rows.length > 1) {
                button.parentElement.remove();
            } else {
                alert("Minimal satu baris harus ada.");
            }
        }

        function bindEvents() {
            document.querySelectorAll('.id-hewan').forEach(select => {
                select.onchange = function () {
                    const parent = this.closest('.form-row');
                    const idHewan = this.value;

                    if (idHewan) {
                        fetch(`tambah_labarugi.php?id_hewan=${idHewan}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data) {
                                    parent.querySelector('.jenis-kelamin').value = data.jenis_kelamin;
                                    parent.querySelector('.jumlah').value = data.jumlah;
                                    parent.querySelector('.hpp').value = data.hpp;
                                    parent.querySelector('.tanggal-pembelian').value = data.tanggal;
                                    parent.querySelector('.total-pakan').value = data.total_pakan;
                                    parent.querySelector('.total-perawatan').value = data.total_perawatan;
                                }
                            });
                    }
                };
            });

            document.querySelectorAll('.harga').forEach(input => {
                input.oninput = function () {
                    const parent = this.closest('.form-row');
                    const hpp = parseFloat(parent.querySelector('.hpp').value) || 0;
                    const harga = parseFloat(this.value) || 0;
                    const pakan = parseFloat(parent.querySelector('.total-pakan').value) || 0;
                    const perawatan = parseFloat(parent.querySelector('.total-perawatan').value) || 0;

                    const totalBiaya = hpp + pakan + perawatan;
                    const keuntungan = harga - totalBiaya;
                    const kerugian = totalBiaya > harga ? totalBiaya - harga : 0;

                    parent.querySelector('.total-keuntungan').value = keuntungan > 0 ? keuntungan.toFixed(2) : 0;
                    parent.querySelector('.total-kerugian').value = kerugian > 0 ? kerugian.toFixed(2) : 0;
                };
            });
        }

        document.addEventListener('DOMContentLoaded', bindEvents);
    </script>


</head>

<body>
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="w3-sidebar-overlay" onclick="w3_close()"></div>

    <!-- Sidebar -->
    <div class="w3-sidebar w3-bar-block w3-border-right w3-light-grey" id="mySidebar">
        <button onclick="w3_close()" class="w3-bar-item w3-button w3-red w3-center close-button">
            <b>Close</b><i class="fa fa-close" style="font-size:20px"></i>
        </button>
        <a href="daftar_hewan.php" class="w3-bar-item w3-button w3-border">Daftar Hewan</a>
        <a href="daftar_pakan.php" class="w3-bar-item w3-button w3-border">Daftar Pakan</a>
        <a href="daftar_perawatan.php" class="w3-bar-item w3-button w3-border">Daftar Perawatan</a>
        <a href="hasil_labarugi.php" class="w3-bar-item w3-button w3-border">Hasil Laba Rugi</a>
        <a href="laporan_labarugi.php" class="w3-bar-item w3-button w3-border">Laporan Laba Rugi</a>
        <a href="scan_code.php" class="w3-bar-item w3-button w3-border">Pindai Kode</a>
        <?php if ($username === 'admin') { ?>
            <a href="daftar_pengguna.php" class="w3-bar-item w3-button w3-border">Daftar Pengguna</a>
        <?php } ?>
        <a href="logout.php" class="w3-bar-item w3-button w3-red w3-center"><b>Log Out </b><i class="fa fa-sign-out"
                style="font-size:20px"></i></a>
    </div>

    <!-- Header -->
    <div class="w3-green" style="display: flex; align-items: center; padding: 10px;">
        <button class="w3-button w3-xlarge" onclick="w3_open()">☰</button>
        <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
            <h3
                style="margin: 0; line-height: 1.5rem; text-align: center; font-size: 25px; margin-top:5px; margin-bottom: 10px;">
                <b>Tambah Laba Rugi</b>
            </h3>
        </div>
    </div>

    <div class="w3-container w3-padding-16">
        <form action="tambah_labarugi.php" method="POST"
            class="w3-container w3-card-4 w3-light-grey w3-padding-16 w3-margin" id="form-labarugi">
            <div id="form-container">
                <div class="form-row">
                    <label>ID Hewan</label>
                    <select name="id_hewan[]" class="w3-input w3-border id-hewan" required>
                        <option value="">Pilih id hewan</option>
                        <?php
                        $result->data_seek(0); // reset pointer result agar bisa diulang
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['id_hewan']}'>{$row['id_hewan']}</option>";
                            }
                        } else {
                            echo "<option value=''>Data Hewan Tidak Ada</option>";
                        }

                        // while ($row = $result->fetch_assoc()) {
                        //     echo "<option value='{$row['id_hewan']}'>{$row['id_hewan']}</option>";
                        // }
                        ?>
                    </select><br>

                    <label>Jenis Kelamin</label>
                    <input type="text" name="jenis_kelamin[]" class="w3-input w3-border jenis-kelamin"
                        style="background-color: aliceblue;" readonly><br>

                    <label>Jumlah</label>
                    <input type="number" name="jumlah[]" class="w3-input w3-border jumlah"
                        style="background-color: aliceblue;" readonly><br>

                    <label>HPP</label>
                    <input type="number" name="hpp[]" class="w3-input w3-border hpp"
                        style="background-color: aliceblue;" readonly><br>

                    <label>Harga</label>
                    <input type="number" name="harga[]" class="w3-input w3-border harga" step="0.01" required><br>

                    <label>Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian[]" class="w3-input w3-border tanggal-pembelian"
                        style="background-color: aliceblue;" readonly><br>

                    <label>Tanggal Penjualan</label>
                    <input type="date" name="tanggal_penjualan[]" class="w3-input w3-border tanggal-penjualan"
                        required><br>

                    <label>Total Pakan</label>
                    <input type="number" name="total_pakan[]" class="w3-input w3-border total-pakan"
                        style="background-color: aliceblue;" readonly><br>

                    <label>Total Perawatan</label>
                    <input type="number" name="total_perawatan[]" class="w3-input w3-border total-perawatan"
                        style="background-color: aliceblue;" readonly><br>

                    <label>Total Keuntungan</label>
                    <input type="number" name="total_keuntungan[]" class="w3-input w3-border total-keuntungan"
                        style="background-color: aliceblue;" readonly><br>

                    <label>Total Kerugian</label>
                    <input type="number" name="total_kerugian[]" class="w3-input w3-border total-kerugian"
                        style="background-color: aliceblue;" readonly><br>

                    <button type="button" onclick="hapusBaris(this)" class="w3-button w3-red w3-small">Hapus
                        Baris</button>
                    <hr>
                </div>
            </div>

            <!-- Tombol Tambah Baris -->
            <button type="button" onclick="tambahBaris()" class="w3-button w3-blue w3-margin-bottom">+ Tambah
                Baris</button><br>
            <div class="w3-row">
                <div class="w3-half">
                    <a href="hasil_labarugi.php" class="w3-gray w3-button w3-container w3-padding-16"
                        style="width: 100%;">Kembali</a>
                </div>
                <div class="w3-half">
                    <input type="submit" class="w3-button w3-green w3-container w3-padding-16" style="width: 100%;"
                        value="Tambah">
                </div>
            </div>
        </form>
    </div>

    <script>
        // JS untuk pemisah ribuan
        function formatRibuan(input) {
            let angka = input.value.replace(/\D/g, ''); // Hanya angka
            input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Tambahkan titik setiap 3 digit
        }

        // untuk buka menu sidebar
        function w3_open() {
            document.getElementById("mySidebar").classList.add("show");
            document.getElementById("sidebarOverlay").classList.add("show");
        }

        // untuk menutup menu sidebar
        function w3_close() {
            document.getElementById("mySidebar").classList.remove("show");
            document.getElementById("sidebarOverlay").classList.remove("show");
        }

        // pesan untuk inputan yang tidak di isi/kosong
        document.addEventListener("DOMContentLoaded", function () {
            var inputs = document.querySelectorAll('input[required], select[required]'); // Tambahkan select[required]

            inputs.forEach(input => {
                input.addEventListener('invalid', function (event) {
                    event.preventDefault();
                    let message = "Mohon diisi, tidak boleh kosong";
                    input.setCustomValidity(message);

                    if (input.validity.valueMissing) {
                        input.reportValidity();
                    }
                });

                input.addEventListener('input', function () {
                    input.setCustomValidity(""); // Reset custom message on input
                });

                // Untuk select, gunakan event change agar reset bekerja saat pengguna memilih opsi
                if (input.tagName === "SELECT") {
                    input.addEventListener('change', function () {
                        input.setCustomValidity("");
                    });
                }
            });
        });
    </script>
</body>

</html>