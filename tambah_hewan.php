<?php
include 'koneksi.php';
include 'phpqrcode/qrlib.php'; // Library untuk QR Code
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $jumlah = intval($_POST['jumlah']);
    $harga = str_replace('.', '', $_POST['harga']);
    $tanggal = $_POST['tanggal'];
    $user_record = $username;
    $date_record = date('Y-m-d H:i:s');

    // Cek apakah ada file gambar yang diunggah
    $gambar_path = "";
    if (!empty($_FILES['gambar']['name'])) {
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Buat folder jika belum ada
        }

        $gambar_name = time() . "_" . basename($_FILES["gambar"]["name"]);
        $target_file = $upload_dir . $gambar_name;
        $gambarFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Hanya izinkan format tertentu
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($gambarFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_path = $target_file;
            } else {
                echo "Gagal mengunggah gambar!";
                exit;
            }
        } else {
            echo "Format gambar tidak didukung! Hanya JPG, JPEG, PNG, dan GIF.";
            exit;
        }
    }

    // Simpan data ke tabel mbek_hewan
    $query = "INSERT INTO mbek_hewan (jenis_kelamin, jumlah, harga, tanggal, date_record, user_record, gambar) 
              VALUES ('$jenis_kelamin', $jumlah, '$harga', '$tanggal', '$date_record', '$user_record', '$gambar_path')";

    if (mysqli_query($conn, $query)) {
        $id_hewan = mysqli_insert_id($conn);

        // Buat QR Code untuk setiap hewan
        $qr_text = "" . $id_hewan;
        $qr_folder = "qrcodes/";
        if (!file_exists($qr_folder)) {
            mkdir($qr_folder, 0777, true);
        }

        $qr_filename = $qr_folder . "qr_hewan_" . $id_hewan . ".png";
        QRcode::png($qr_text, $qr_filename, QR_ECLEVEL_L, 5);

        // Simpan QR link ke database
        $query_qr = "UPDATE mbek_hewan SET qr_link='$qr_filename' WHERE id_hewan=$id_hewan";


        mysqli_query($conn, $query_qr);

        header('Location: daftar_hewan.php');
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
    <title>Tambah Hewan</title>
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
                <b>Tambah Hewan</b>
            </h3>
        </div>
    </div>

    <div class="w3-container w3-padding-16">
        <form action="" method="post" enctype="multipart/form-data"
            class="w3-container w3-card-4 w3-light-grey w3-padding-16 w3-margin">

            <label>Jenis Kelamin</label>
            <select class="w3-input w3-border" name="jenis_kelamin" required>
                <option value="">Pilih jenis kelamin</option>
                <option value="Jantan">Jantan</option>
                <option value="Betina">Betina</option>
            </select><br>
            <label>Jumlah</label>
            <input type="number" class="w3-input w3-border" name="jumlah" required><br>
            <label>Harga</label>
            <input type="text" id="harga" class="w3-input w3-border" name="harga" required
                oninput="formatRibuan(this)"></label><br>
            <label>Tanggal</label>
            <input type="date" class="w3-input w3-border" name="tanggal" required><br>
            <label>Unggah Gambar</label>
            <input type="file" class="w3-input w3-border" name="gambar" accept="image/*"><br>
            <div class="w3-half">
                <a href="daftar_hewan.php" class="w3-gray w3-button w3-container w3-padding-16"
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