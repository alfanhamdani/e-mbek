<?php
include "koneksi.php";
session_start(); // Mulai sesi untuk mengakses informasi sesi pengguna

// Pastikan pengguna telah login sebelumnya
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect jika pengguna belum login
    exit;
}

$username = $_SESSION['username']; // Ambil username pengguna dari sesi
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
    <link rel="stylesheet" href="w3.css">
    <link rel="icon" href="logo e-mbek.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* Modified styles for sidebar */
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

        .w3-sidebar.hide {
            left: -250px;
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

        /* Styling for sidebar overlay */
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

<body class="w3-light-grey">
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

    <!-- Page Content -->
    <div id="mainContent" style="margin-left: 0; transition: margin-left 0.5s;">
        <div class="w3-green sticky-header" style="display: flex; align-items: center; padding: 10px;">
            <button class="w3-button w3-xlarge" onclick="w3_open()">☰</button>
            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
                <h3
                    style="margin: 0; line-height: 1.5rem; text-align: center; font-size: 25px; margin-top:5px; margin-bottom: 10px;">
                    <b>Hasil Laba Rugi</b>
                </h3>
                <div>
                    <!-- Form untuk memilih bulan -->
                    <form action="laporan_labarugi.php" method="POST" class="w3-center">
                        <select name="bulan" class="w3-select w3-border" style="width: 120px;">
                            <option value="" disabled selected>Pilih Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <select name="tahun" class="w3-select w3-border" style="width: 120px;">
                            <option value="" disabled selected>Pilih Tahun</option>
                            <?php
                            $startYear = 2025;
                            $currentYear = date("Y");
                            for ($year = $startYear; $year <= $currentYear; $year++) {
                                echo "<option value=\"$year\">$year</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="w3-button"
                            style="background-color: darkgoldenrod">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="w3-container">
        <br>
        <?php
        // Cek jika form sudah disubmit
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["bulan"]) && !empty($_POST["tahun"])) {
            // Ambil bulan dan tahun dari form
            $bulan = $_POST["bulan"];
            $tahun = $_POST["tahun"];

            $stmt = $conn->prepare("
        SELECT ?, 
            (SELECT COALESCE(SUM(total_keuntungan), 0) FROM mbek_hasil_labarugi WHERE MONTH(tanggal_penjualan) = ? AND YEAR(tanggal_penjualan) = ?) AS total_keuntungan, 
            (SELECT COALESCE(SUM(total_kerugian), 0) FROM mbek_hasil_labarugi WHERE MONTH(tanggal_penjualan) = ? AND YEAR(tanggal_penjualan) = ?) AS total_kerugian
        ");
            $stmt->bind_param("siiii", $bulan, $bulan, $tahun, $bulan, $tahun);
            $stmt->execute();
            $result = $stmt->get_result();

            // Menampilkan data jika ada hasil
            if ($result->num_rows > 0) {
                echo '<div class="w3-container w3-margin-top">';
                echo '<div class="w3-responsive">';
                echo '<div class="w3-table-all w3-card-4">';
                echo '<div class="w3-row w3-green w3-padding-small w3-bold">';
                echo '<div class="w3-col s4 m4 l4">Bulan</div>';
                echo '<div class="w3-col s4 m4 l4">Total Keuntungan</div>';
                echo '<div class="w3-col s4 m4 l4">Total Kerugian</div>';
                echo '</div>';

                while ($row = $result->fetch_assoc()) {
                    echo '<div class="w3-row w3-padding-small w3-hover-light-grey">';
                    echo '<div class="w3-col s4 m4 l4">' . htmlspecialchars($bulan) . '</div>';
                    echo '<div class="w3-col s4 m4 l4">Rp. ' . number_format($row['total_keuntungan'], 0, ',', '.') . '</div>';
                    echo '<div class="w3-col s4 m4 l4">Rp. ' . number_format($row['total_kerugian'], 0, ',', '.') . '</div>';
                    echo '</div>';
                }

                echo '</div>'; // end w3-table-all
                echo '</div>'; // end w3-responsive
                echo '</div>'; // end w3-container
            } else {
                echo '<p class="w3-center w3-text-red w3-padding">Tidak ada data untuk bulan <b>' . htmlspecialchars($bulan) . '</b> dan tahun <b>' . htmlspecialchars($tahun) . '</b>.</p>';
            }

            $stmt->close();
            $conn->close();
        }
        ?>
    </div>


    <!-- Responsive CSS -->
    <style>
        .responsive-table {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        .table-header,
        .table-cell {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            text-align: right;
        }

        .table-header {
            font-weight: bold;
            background-color: #f1f1f1;
            text-align: center;
        }

        /* Layout adjustments for smaller screens */
        @media (max-width: 768px) {

            .table-header,
            .table-cell {
                flex-basis: 100%;
                /* Full width on smaller screens */
                text-align: left;
            }
        }

        @media (min-width: 769px) {

            .table-header,
            .table-cell {
                flex-basis: calc(100% / 7);
                /* Evenly distribute columns on larger screens */
            }
        }
    </style>


    <script>
        function w3_open() {
            document.getElementById("mySidebar").classList.add("show");
            document.getElementById("sidebarOverlay").classList.add("show");
        }

        function w3_close() {
            document.getElementById("mySidebar").classList.remove("show");
            document.getElementById("sidebarOverlay").classList.remove("show");
        }
    </script>

</body>

</html>