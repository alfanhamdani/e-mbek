<?php
include 'koneksi.php';
session_start(); // Mulai sesi untuk mengakses informasi sesi pengguna

// Pastikan pengguna telah login sebelumnya
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect jika pengguna belum login
    exit;
}

$id_hewan = isset($_GET['id_hewan']) ? mysqli_real_escape_string($conn, $_GET['id_hewan']) : '';
$query = "SELECT * FROM mbek_hewan WHERE id_hewan = '$id_hewan'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_hewan = $_POST['id_hewan'];
    $nama_baru = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $tanggal = $_POST['tanggal'];
    $user_modified = 'admin'; // Sesuaikan dengan pengguna yang login
    $date_modified = date('Y-m-d H:i:s');

    $query = "UPDATE mbek_hewan 
              SET nama = '$nama_baru', jenis_kelamin = '$jenis_kelamin', jumlah = $jumlah, 
                  harga = $harga, tanggal = '$tanggal', date_modified = '$date_modified', user_modified = '$user_modified' 
              WHERE id_hewan = '$id_hewan'";
    if (mysqli_query($conn, $query)) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="logo e-mbek.png">
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

        /* Custom Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .w3-input-container {
            position: relative;
        }

        .w3-input-container input[type="password"] {
            padding-right: 40px;
            /* space for the eye icon */
        }

        .w3-input-container i {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 18px;
            color: #555;
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
        <a href="daftar_pengguna.php" class="w3-bar-item w3-button w3-border">Daftar Pengguna</a>
        <a href="logout.php" class="w3-bar-item w3-button w3-red w3-center"><b>Log Out </b><i class="fa fa-sign-out"
                style="font-size:20px"></i></a>
    </div>

    <!-- Page Content -->
    <div class="w3-green" style="display: flex; align-items: center; padding: 10px;">
        <button class="w3-button w3-xlarge" onclick="w3_open()">☰</button>
        <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
            <h3
                style="margin: 0; line-height: 1.5rem; text-align: center; font-size: 25px; margin-top:5px; margin-bottom: 10px;">
                <b>Edit Pengguna</b>
            </h3>
        </div>
    </div>


    <form method="post" action="" class="w3-container w3-card-4 w3-light-grey w3-padding-16 w3-margin">
    <label>ID Hewan: <input type="text" class="w3-input w3-border" name="id_hewan" value="<?= $data['id_hewan'] ?>" readonly></label><br>
    <label>Nama Hewan: <input type="text" class="w3-input w3-border" id="nama" name="nama" value="<?= $data['nama'] ?>" required></label><br>
    <label>Jenis Kelamin: 
        <select class="w3-input w3-border" id="jenis_kelamin" name="jenis_kelamin" required>
            <option value="L" <?= $data['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= $data['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select>
    </label><br>
    <label>Jumlah: <input class="w3-input w3-border" type="number" id="jumlah" name="jumlah" value="<?= $data['jumlah'] ?>" required></label><br>
    <label>Harga: <input class="w3-input w3-border" type="number" id="harga" name="harga" value="<?= $data['harga'] ?>" required></label><br>
    <label>Tanggal: <input class="w3-input w3-border" type="date" id="tanggal" name="tanggal" value="<?= $data['tanggal'] ?>" required></label><br>
    <br>
    <div class="w3-half">
        <a href="daftar_hewan.php" class="w3-gray w3-button w3-container w3-padding-16" style="width: 100%;">Kembali</a>
    </div>
    <div class="w3-half">
        <button type="submit" id="updateButton" class="w3-button w3-blue w3-container w3-padding-16" style="width: 100%;" disabled>Simpan</button>
    </div>
</form>


<!-- JavaScript -->
<script>
        function w3_open() {
            document.getElementById("mySidebar").classList.add('show');
            document.getElementById("sidebarOverlay").classList.add('show');
            document.body.addEventListener('click', closeSidebarOutside);
        }

        function w3_close() {
            document.getElementById("mySidebar").classList.remove('show');
            document.getElementById("sidebarOverlay").classList.remove('show');
            document.body.removeEventListener('click', closeSidebarOutside);
        }

        function closeSidebarOutside(event) {
            if (!event.target.closest('#mySidebar') && !event.target.closest('.w3-xlarge')) {
                w3_close();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
    var originalValues = {
        nama: "<?= $data['nama']; ?>",
        jenis_kelamin: "<?= $data['jenis_kelamin']; ?>",
        jumlah: "<?= $data['jumlah']; ?>",
        harga: "<?= $data['harga']; ?>",
        tanggal: "<?= $data['tanggal']; ?>"
    };

    function checkChanges() {
        var currentValues = {
            nama: document.getElementById('nama').value,
            jenis_kelamin: document.getElementById('jenis_kelamin').value,
            jumlah: document.getElementById('jumlah').value,
            harga: document.getElementById('harga').value,
            tanggal: document.getElementById('tanggal').value
        };

        var updateButton = document.getElementById('updateButton');

        // Periksa jika ada perubahan dari nilai awal
        if (JSON.stringify(originalValues) !== JSON.stringify(currentValues)) {
            updateButton.disabled = false;
        } else {
            updateButton.disabled = true;
        }
    }

    // Tambahkan event listener untuk mendeteksi perubahan
    document.getElementById('nama').addEventListener('input', checkChanges);
    document.getElementById('jenis_kelamin').addEventListener('change', checkChanges);
    document.getElementById('jumlah').addEventListener('input', checkChanges);
    document.getElementById('harga').addEventListener('input', checkChanges);
    document.getElementById('tanggal').addEventListener('change', checkChanges);
});
    

        document.addEventListener("DOMContentLoaded", function () {
            var inputs = document.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('invalid', function (event) {
                    event.preventDefault();
                    // Custom validation message
                    let message = "Mohon diisi, tidak boleh kosong";
                    input.setCustomValidity(message);
                    // Display the message
                    if (input.validity.valueMissing) {
                        input.reportValidity();
                    }
                });

                input.addEventListener('input', function () {
                    input.setCustomValidity(""); // Reset custom message on input
                });
            });
        });
    </script>
</body>
</html>
