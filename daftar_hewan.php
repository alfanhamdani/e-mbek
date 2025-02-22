<?php
include 'koneksi.php';
include 'phpqrcode/qrlib.php';

session_start(); // Mulai sesi untuk mengakses informasi sesi pengguna

// Pastikan pengguna telah login sebelumnya
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect jika pengguna belum login
    exit;
}

$username = $_SESSION['username']; // Ambil username pengguna dari sesi

// Handle pencarian
$search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query untuk menghitung total jumlah hewan dengan pencarian
$sql_total_hewan = "SELECT COUNT(*) AS total_hewan FROM mbek_hewan WHERE id_hewan LIKE '%$search_keyword%'";
$result_total = mysqli_query($conn, $sql_total_hewan);

if ($result_total) {
    $data_total = mysqli_fetch_assoc($result_total);
    $totalHewan = $data_total['total_hewan'];
} else {
    $totalHewan = 0; // Fallback jika terjadi kesalahan
}

// Jumlah hewan per halaman
$records_per_page = 10;

// Menghitung jumlah halaman
$total_pages = ceil($totalHewan / $records_per_page);

// Mendapatkan halaman saat ini
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// Menghitung offset untuk query SQL
$offset = ($page - 1) * $records_per_page;

// Query untuk mengambil data hewan dengan pencarian dan pagination
$queryHewan = "SELECT * FROM mbek_hewan WHERE id_hewan LIKE '%$search_keyword%' ORDER BY jenis_kelamin DESC LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $queryHewan);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hewan</title>
    <link rel="stylesheet" href="w3.css">
    <link rel="icon" href="logo e-mbek.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .action-icons {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }

        .bottom-right {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

        .bottom-right i {
            margin: 0;
        }

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

        @media (max-width: 600px) {
            .w3-table-all {
                table-layout: fixed;
                width: 100%;
            }

            .w3-table-all th,
            .w3-table-all td {
                word-wrap: break-word;
            }

            .w3-table-all th:nth-of-type(1),
            .w3-table-all td:nth-of-type(1) {
                width: 40%;
            }

            .w3-table-all th:nth-of-type(2),
            .w3-table-all td:nth-of-type(2) {
                width: 30%;
            }

            .w3-table-all th:nth-of-type(3),
            .w3-table-all td:nth-of-type(3) {
                width: 30%;
            }
        }

        .action-icons a {
            margin: 5px;
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
        <?php if ($username === 'admin') { ?>
            <a href="daftar_pengguna.php" class="w3-bar-item w3-button w3-border">Daftar Pengguna</a>
        <?php } ?>
        <a href="logout.php" class="w3-bar-item w3-button w3-red w3-center"><b>Log Out </b><i class="fa fa-sign-out"
                style="font-size:20px"></i></a>
    </div>

    <!-- Page Content -->
    <div id="mainContent" style="margin-left: 0; transition: margin-left 0.5s;">
        <div class="w3-green" style="display: flex; align-items: center; padding: 10px;">
            <button class="w3-button w3-xlarge" onclick="w3_open()">☰</button>
            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
                <h3
                    style="margin: 0; line-height: 1.5rem; text-align: center; font-size: 25px; margin-top:5px; margin-bottom: 10px;">
                    <b>Daftar Hewan</b>
                </h3>
            </div>
        </div>

        <!-- Modal untuk konfirmasi penghapusan -->
        <div id="deleteModal" class="w3-modal" onclick="closeModal(event)"
            style="align-items:center; padding-top: 15%;">
            <div class="w3-modal-content w3-animate-top w3-card-4">
                <header class="w3-container w3-red">
                    <span onclick="document.getElementById('deleteModal').style.display='none'"
                        class="w3-button w3-display-topright">&times;</span>
                    <h2>Konfirmasi</h2>
                </header>
                <div class="w3-container">
                    <p>Apakah Anda yakin ingin menghapus hewan ini?</p>
                    <div class="w3-right">
                        <button class="w3-button w3-grey"
                            onclick="document.getElementById('deleteModal').style.display='none'">Batal</button>
                        <button class="w3-button w3-red" onclick="confirmDelete()">Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- CSS untuk modal konfirmasi delete -->
        <style>
            .w3-modal {
                display: none;
                /* Sembunyikan modal secara default */
                position: fixed;
                z-index: 9999;
                /* Pastikan modal berada di atas semua elemen lain */
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.4);
                /* Latar belakang gelap semi-transparan */
            }
        </style>

        <!-- Kotak Pencarian -->
        <div style="display: flex; justify-content: center; margin: 20px;">
            <form method="GET" action="" style="width: 100%; max-width: 600px; display: flex; position: relative;">
                <!-- Input Field -->
                <input type="text" name="search" id="searchInput" class="w3-input w3-border"
                    placeholder="Cari id hewan..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="width: 100%; padding: 12px 20px; padding-right: 60px; border-radius: 50px; 
                   border: 2px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); font-size: 16px;">

                <!-- Tombol "Cari" -->
                <button type="submit" class="w3-button w3-green" style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); height: 40px; width: 40px; 
                   border-radius: 50%; border: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); cursor: pointer; 
                   display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-search" style="font-size: 18px;"></i>
                </button>
            </form>
        </div>


        <!-- Total users -->
        <div style="font-size: 15px; text-align: right; padding-right: 30px;">
            <span class="w3-bar-item">Total: <?php echo $totalHewan; ?> Hewan</span>
        </div>

        <!-- Table of Users -->
        <div class="w3-responsive">
            <table class="w3-table-all w3-centered" border="1" style="border-collapse: collapse; width: 100%;">
                <tr class="w3-green">
                    <th>ID Hewan</th>
                    <th>Jenis Kelamin</th>
                    <th>Harga</th>
                    <th>QR Code</th>
                    <th>image</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr class="hewan-row">
                        <td style="font-size: 15px;"><?php echo htmlspecialchars($row['id_hewan']); ?></td>
                        <td style="font-size: 15px;"><?php echo htmlspecialchars($row['jenis_kelamin']); ?></td>
                        <td style="font-size: 15px;">Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td>
    <img src="<?php echo $row['qr_link']; ?>" alt="QR Code" width="100" height="100">
</td>



             <td><img src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="Gambar Hewan" style="width: 100px; height: auto;"></td>

                        <td style="font-size: 14px; text-align: center;">
                            <!-- Tombol Lihat Lainnya -->
                            <button
                                onclick="document.getElementById('detailModal<?= $row['id_hewan'] ?>').style.display='block'"
                                class="w3-button w3-grey w3-round w3-small">
                                Lihat Lainnya
                            </button>
                            <a href="edit_hewan.php?id_hewan=<?php echo $row['id_hewan']; ?>"
                                class="material-icons w3-yellow w3-btn w3-button w3-round"
                                style="font-size: 15px;">&#xe22b;</a>
                            <a href="#"
                                onclick="deleteHewan('<?php echo htmlspecialchars($row['id_hewan']); ?>', '<?php echo htmlspecialchars($row['id_hewan']); ?>')"
                                class="fa fa-trash w3-btn w3-button w3-round w3-red" style="font-size: 15px;"></a>
                        </td>
                    </tr>

                    <!-- Modal Detail -->
                    <div id="detailModal<?= $row['id_hewan'] ?>" class="w3-modal">
                        <div class="w3-modal-content w3-card-4 w3-animate-top" style="max-width:600px">
                            <header class="w3-container w3-center w3-green">
                                <span
                                    onclick="document.getElementById('detailModal<?= $row['id_hewan'] ?>').style.display='none'"
                                    class="w3-button w3-display-topright">&times;</span>
                                <h3>
                                    <b>Detail Pakan</b>
                                </h3>
                            </header>
                            <div class="w3-container">
                                <p><strong>ID Hewan:</strong> <?= htmlspecialchars($row['id_hewan']); ?></p>
                                <p><strong>Jenis Kelamin:</strong> <?= htmlspecialchars($row['jenis_kelamin']); ?></p>
                                <p><strong>Jumlah:</strong> <?= htmlspecialchars($row['jumlah']); ?></p>
                                <p><strong>Harga:</strong> Rp.
                                    <?= number_format($row['harga'], 0, ',', '.'); ?>
                                </p>
                                <p><strong>Tanggal:</strong> <?= htmlspecialchars($row['tanggal']); ?></p>
                            </div>
                            <footer class="w3-container">
                                <button
                                    onclick="document.getElementById('detailModal<?= $row['id_hewan'] ?>').style.display='none'"
                                    class="w3-button w3-red w3-right">Tutup</button>
                            </footer>
                        </div>
                    </div>
                <?php } ?>
            </table>
        </div>

        <!-- Modern Pagination with Slightly Rectangular Corners -->
        <div class="pagination-container">
            <!-- Previous Button -->
            <a href="?page=<?php echo max(1, $page - 1); ?>" class="pagination-button">&laquo;</a>

            <!-- Page Number Buttons -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="pagination-button <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <!-- Next Button -->
            <a href="?page=<?php echo min($total_pages, $page + 1); ?>" class="pagination-button">&raquo;</a>
        </div>
        <!-- CSS Styles for Pagination with Slightly Rectangular Corners -->
        <style>
            .pagination-container {
                display: flex;
                align-items: center;
                margin: 20px 0;
                padding-left: 10px;
            }

            .pagination-button {
                margin: 0 5px;
                padding: 8px 16px;
                border-radius: 6px;
                border: 1px solid #ddd;
                background-color: #f5f5f5;
                color: #333;
                text-decoration: none;
                font-size: 14px;
                font-weight: normal;
                transition: background-color 0.3s, color 0.3s;
            }

            .pagination-button:hover {
                color: white;
                background-color: #4CAF50;
                border-color: #4CAF50;
            }

            .pagination-button.active {
                background-color: #4CAF50;
                color: white;
                border-color: #4CAF50;
                cursor: default;
                pointer-events: none;
            }

            .pagination-button:focus {
                outline: none;
            }
        </style>

        <!-- tombol untuk nambah -->
        <a href="tambah_hewan.php" class="w3-btn w3-round-xlarge w3-green bottom-right">
            <i class="fa fa-plus" style="font-size:30px"></i>
        </a>

        <!-- JavaScript -->
        <script>
            function w3_open() {
                document.getElementById("mySidebar").classList.add('show');
                document.getElementById("sidebarOverlay").classList.add('show');
            }

            function w3_close() {
                document.getElementById("mySidebar").classList.remove('show');
                document.getElementById("sidebarOverlay").classList.remove('show');
            }

            function deleteHewan(id_hewan) {
                var modal = document.getElementById('deleteModal');
                modal.style.display = 'block'; // Tampilkan modal konfirmasi

                var modalMessage = modal.querySelector('p');
                modalMessage.textContent = "Apakah Anda yakin ingin menghapus hewan ini?";

                // Simpan nama hewan yang akan dihapus di button 'Hapus'
                var confirmButton = modal.querySelector('.w3-button.w3-red');
                confirmButton.onclick = function () {
                    window.location.href = "hapus_hewan.php?id_hewan=" + encodeURIComponent(id_hewan);
                };
            }

            function searchItems() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("searchInput");
                filter = input.value.toUpperCase();
                table = document.querySelector("table");
                tr = table.getElementsByClassName("hewan-row");

                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[0];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
            }


        </script>
    </div>
</body>

</html>

<?php mysqli_close($conn); ?>