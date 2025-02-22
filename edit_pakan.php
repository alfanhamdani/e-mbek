<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username'];
$id = $_GET['id'] ?? '';
$id_pakan = $_GET['id_pakan'] ?? '';

$query = "SELECT * FROM mbek_pakan WHERE id_pakan = '$id_pakan'";
$result = mysqli_query($conn, $query);
$data_pakan = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_hewan = $_POST['id_hewan'];
    $jenis_pakan = $_POST['jenis_pakan'];
    $berat = $_POST['berat'];
    $harga_pakan = str_replace('.', '', $_POST['harga_pakan']);
    $tanggal = $_POST['tanggal'];
    $user_modified = $username;
    $date_modified = date('Y-m-d H:i:s');
    
    // Cek apakah pengguna mengunggah gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                // Hapus gambar lama jika ada
                if (!empty($data_pakan['gambar']) && file_exists("uploads/" . $data_pakan['gambar'])) {
                    unlink("uploads/" . $data_pakan['gambar']);
                }
                
                $gambar = basename($_FILES["gambar"]["name"]);
                $query = "UPDATE mbek_pakan SET id_hewan='$id_hewan', jenis_pakan='$jenis_pakan', berat='$berat', harga_pakan='$harga_pakan', tanggal='$tanggal', date_modified='$date_modified', user_modified='$user_modified', gambar='$gambar' WHERE id_pakan='$id_pakan'";
            }
        } else {
            echo "Format gambar tidak valid.";
            exit;
        }
    } else {
        $query = "UPDATE mbek_pakan SET id_hewan='$id_hewan', jenis_pakan='$jenis_pakan', berat='$berat', harga_pakan='$harga_pakan', tanggal='$tanggal', date_modified='$date_modified', user_modified='$user_modified' WHERE id_pakan='$id_pakan'";
    }
    
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="logo e-mbek.png">
    <title>Edit Pakan</title>
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
        <?php if ($username === 'admin') { ?>
            <a href="daftar_pengguna.php" class="w3-bar-item w3-button w3-border">Daftar Pengguna</a>
        <?php } ?>
        <a href="logout.php" class="w3-bar-item w3-button w3-red w3-center"><b>Log Out </b><i class="fa fa-sign-out"
                style="font-size:20px"></i></a>
    </div>

    <!-- Page Content -->
    <div class="w3-green" style="display: flex; align-items: center; padding: 10px;">
        <button class="w3-button w3-xlarge" onclick="w3_open()">☰</button>
        <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
            <h3
                style="margin: 0; line-height: 1.5rem; text-align: center; font-size: 25px; margin-top:5px; margin-bottom: 10px;">
                <b>Edit Pakan</b>
            </h3>
        </div>
    </div>

    <form method="post" action="" enctype="multipart/form-data" class="w3-container w3-card-4 w3-light-grey w3-padding-16 w3-margin">
        <label>ID Hewan</label>
        <select class="w3-input w3-border" id="id_hewan" name="id_hewan" required>
            <option value="">Pilih ID Hewan</option>
            <?php
            $query = "SELECT id_hewan FROM mbek_hewan ORDER BY id_hewan ASC";
            $result = mysqli_query($conn, $query);

            if ($result) {
                while ($data = mysqli_fetch_assoc($result)) {
                    $selected = ($data['id_hewan'] == $data_pakan['id_hewan']) ? 'selected' : '';
                    echo "<option value='" . $data['id_hewan'] . "' $selected>" . $data['id_hewan'] . "</option>";
                }
            }
            ?>
        </select><br>

        <label>Jenis Pakan</label>
        <select class="w3-input w3-border" id="jenis_pakan" name="jenis_pakan" required>
            <option value="Rumput" <?= isset($data_pakan['jenis_pakan']) && $data_pakan['jenis_pakan'] === 'Rumput' ? 'selected' : '' ?>>Rumput</option>
            <option value="Pur" <?= isset($data_pakan['jenis_pakan']) && $data_pakan['jenis_pakan'] === 'Pur' ? 'selected' : '' ?>>Pur</option>
        </select><br>

        <label>Berat(g/kg)</label>
        <input class="w3-input w3-border" type="text" id="berat" name="berat"
            value="<?= isset($data_pakan['berat']) ? $data_pakan['berat'] : '' ?>" required><br>

        <label>Harga Pakan</label>
        <input class="w3-input w3-border" type="text" id="harga_pakan" name="harga_pakan"
            value="<?= isset($data_pakan['harga_pakan']) ? number_format($data_pakan['harga_pakan'], 0, ',', '.') : '' ?>"
            required oninput="formatRibuan(this)"><br>

        <label>Tanggal</label>
        <input class="w3-input w3-border" type="date" id="tanggal" name="tanggal"
            value="<?= isset($data_pakan['tanggal']) ? $data_pakan['tanggal'] : '' ?>" required><br>

        <br>
        <label>Gambar Pakan</label>
    <input class="w3-input w3-border" type="file" name="gambar">
    <?php if (!empty($data_pakan['gambar'])): ?>
        <p>Gambar saat ini:</p>
        <img src="uploads/<?= $data_pakan['gambar'] ?>" width="100">
    <?php endif; ?>
    <br>
    <br>
        <div class="w3-half">
            <a href="daftar_pakan.php" class="w3-gray w3-button w3-container w3-padding-16"
                style="width: 100%;">Kembali</a>
        </div>
        <div class="w3-half">
            <button type="submit" id="updateButton" class="w3-button w3-blue w3-container w3-padding-16"
                style="width: 100%;">Simpan</button>
        </div>
    </form>

    <!-- JavaScript -->
    <script>
        // JS untuk pemisah ribuan
        function formatRibuan(input) {
            let angka = input.value.replace(/\D/g, ''); // Hanya angka
            input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Tambahkan titik setiap 3 digit
        }

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
            // Fungsi untuk menghapus titik pada angka (pemisah ribuan)
            function cleanNumber(value) {
                return value.replace(/\./g, '');
            }

            // Simpan nilai awal dari input
            var originalValues = {
                id_hewan: document.getElementById('id_hewan').value.trim(),
                jenis_pakan: document.getElementById('jenis_pakan').value.trim(),
                berat: document.getElementById('berat').value.trim(),
                harga_pakan: cleanNumber(document.getElementById('harga_pakan').value.trim()),
                tanggal: document.getElementById('tanggal').value.trim()
            };

            // Fungsi untuk memeriksa perubahan
            function checkChanges() {
                var currentValues = {
                    id_hewan: document.getElementById('id_hewan').value.trim(),
                    jenis_pakan: document.getElementById('jenis_pakan').value.trim(),
                    berat: document.getElementById('berat').value.trim(),
                    harga_pakan: cleanNumber(document.getElementById('harga_pakan').value.trim()),
                    tanggal: document.getElementById('tanggal').value.trim()
                };

                var updateButton = document.getElementById('updateButton');
                updateButton.disabled = JSON.stringify(originalValues) === JSON.stringify(currentValues);
            }

            // Tambahkan event listener untuk mendeteksi perubahan
            document.getElementById('id_hewan').addEventListener('change', checkChanges);
            document.getElementById('jenis_pakan').addEventListener('change', checkChanges);
            document.getElementById('berat').addEventListener('input', checkChanges);
            document.getElementById('harga_pakan').addEventListener('input', checkChanges);
            document.getElementById('tanggal').addEventListener('change', checkChanges);

            // Pastikan tombol Simpan awalnya dinonaktifkan
            checkChanges();
        });

        // Fungsi untuk memformat angka dengan pemisah ribuan
        function formatRibuan(input) {
            let angka = input.value.replace(/\D/g, ''); // Hapus karakter non-angka
            input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Tambahkan titik ribuan
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