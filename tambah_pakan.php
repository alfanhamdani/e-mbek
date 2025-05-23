<?php
include 'koneksi.php';
session_start();

// Pastikan pengguna telah login
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username']; // Ambil username dari sesi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_hewan = $_POST['id_hewan'];
    $jenis_pakan = $_POST['jenis_pakan'];
    $berat = $_POST['berat'];
    $harga_pakan = str_replace('.', '', $_POST['harga_pakan']); // Hapus pemisah ribuan
    $tanggal = $_POST['tanggal'];
    $user_record = $username;
    $date_record = date('Y-m-d H:i:s');

    // Query untuk menyimpan data ke database
    $query = "INSERT INTO mbek_pakan (id_hewan, jenis_pakan, berat, harga_pakan, tanggal, date_record, user_record) 
              VALUES ('$id_hewan', '$jenis_pakan', '$berat', '$harga_pakan', '$tanggal', '$date_record', '$user_record')";

    if (mysqli_query($conn, $query)) {
        header('Location: daftar_pakan.php');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="stylesheet" href="w3.css">
    <link rel="icon" href="logo e-mbek.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Tambah Pakan</title>
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
                <b>Tambah Pakan</b>
            </h3>
        </div>
    </div>

    <div class="w3-container w3-padding-16">
        <form action="" method="post" enctype="multipart/form-data"
            class="w3-container w3-card-4 w3-light-grey w3-padding-16 w3-margin">
            <label>ID Hewan</label>
            <select class="w3-input w3-border" name="id_hewan" required>
                <option value="">Pilih id hewan</option>
                <?php
                // Ambil data ID Hewan dari tabel mbek_hewan
                $query = "SELECT id_hewan FROM mbek_hewan WHERE void = 0 ORDER BY id_hewan ASC";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['id_hewan'] . "'>" . $row['id_hewan'] . "</option>";
                    }
                }
                ?>
            </select><br>
            <label>Jenis Pakan</label>
            <select class="w3-input w3-border" name="jenis_pakan" required>
                <option value="">Pilih jenis pakan</option>
                <option value="Rumput">Rumput</option>
                <option value="Pur">Pur</option>
            </select><br>
            <label>Berat(g/kg)</label>
            <input type="text" class="w3-input w3-border" name="berat" required><br>
            <label>Harga Pakan</label>
            <input type="text" id="harga_pakan" class="w3-input w3-border" name="harga_pakan" required
                oninput="formatRibuan(this)"></label><br>
            <label>Tanggal</label>
            <input type="date" class="w3-input w3-border" name="tanggal" value="<?php echo date('Y-m-d'); ?>"
                required><br>

            <div class="w3-half">
                <a href="daftar_pakan.php" class="w3-gray w3-button w3-container w3-padding-16"
                    style="width: 100%;">Kembali</a>
            </div>
            <div class="w3-half">
                <input type="submit" class="w3-button w3-green w3-container w3-padding-16" style="width: 100%;"
                    value="Tambah">
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