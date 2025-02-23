<?php
include 'koneksi.php';
session_start();

// Pastikan pengguna telah login sebelumnya
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect jika pengguna belum login
    exit;
}

$username = $_SESSION['username']; // Ambil username pengguna dari sesi

// Handle search keyword
$search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Process deletion (mark as void) if 'action' and 'id' parameters are set
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_penjualan = mysqli_real_escape_string($conn, $_GET['id']);

    // Mark the sale as void
    $sql_delete = "UPDATE mbek_hasil_labarugi SET void = 1 WHERE id_hasil_labarugi = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, 'i', $id_penjualan);

    if (mysqli_stmt_execute($stmt_delete)) {
        header('Location: hasil_labarugi.php'); // Redirect after "deletion"
        exit;
    } else {
        die("Error: " . mysqli_error($conn));
    }
}

// Count total sales for pagination
$sql_count = "SELECT COUNT(id_hasil_labarugi) AS total 
              FROM mbek_hasil_labarugi 
              WHERE void = 0 AND user_record = ?";

$stmt_count = mysqli_prepare($conn, $sql_count);
mysqli_stmt_bind_param($stmt_count, 's', $username);
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];

// Pagination settings
$records_per_page = 10;
$total_pages = ceil($total_records / $records_per_page);
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Query to get sales data with search and pagination
$sql = "SELECT id_hasil_labarugi, id_hewan, jenis_kelamin, jumlah, hpp, harga, tanggal_pembelian, tanggal_penjualan, user_record 
        FROM mbek_hasil_labarugi 
        WHERE void = 0 
        AND user_record = ? 
        AND (id_hewan LIKE ? OR jenis_kelamin LIKE ?)
        ORDER BY id_hasil_labarugi DESC 
        LIMIT ?, ?";

$stmt = mysqli_prepare($conn, $sql);
$search_keyword_like = '%' . $search_keyword . '%';
mysqli_stmt_bind_param($stmt, 'ssssi', $username, $search_keyword_like, $search_keyword_like, $offset, $records_per_page);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Query to get the total number of sales
$totalQuery = "SELECT COUNT(*) AS total_sales FROM mbek_hasil_labarugi WHERE void = 0 AND user_record = ?";

$stmt_total = mysqli_prepare($conn, $totalQuery);
mysqli_stmt_bind_param($stmt_total, 's', $username);
mysqli_stmt_execute($stmt_total);
$totalResult = mysqli_stmt_get_result($stmt_total);

if ($totalResult) {
    $totalData = mysqli_fetch_assoc($totalResult);
    $totalSales = $totalData['total_sales'];
} else {
    $totalSales = 0; // Fallback in case of an error
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Laba Rugi</title>
    <link rel="stylesheet" href="w3.css">
    <link rel="icon" href="logo e-mbek.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Styling for specific elements */
        .action-icons {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }

        .sticky-header {
            position: -webkit-sticky;
            /* For Safari */
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: white;
            /* Ensure background is white to cover content below */
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

        .w3-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
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
        <a href="scan_code.php" class="w3-bar-item w3-button w3-border">Pindai Kode</a>
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
                    <b>Hasil Laba Rugi</b>
                </h3>
            </div>
        </div>

        <!-- Search Box -->
        <div style="display: flex; justify-content: center; margin: 20px;">
            <form method="GET" action="" style="width: 100%; max-width: 600px; display: flex; position: relative;">
                <!-- Input Field with Modern Style -->
                <input type="text" name="search" class="w3-input w3-border" placeholder="Cari id hewan..."
                    value="<?php echo isset($_GET['search']) && !empty($_GET['search']) ? '' : htmlspecialchars($search_keyword); ?>"
                    style="width: 100%; padding: 12px 20px; padding-right: 60px; border-radius: 50px; border: 2px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); font-size: 16px;">

                <!-- Modern "Cari" Button -->
                <button type="submit" class="w3-button w3-green"
                    style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); height: 40px; width: 40px; border-radius: 50%; border: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-search" style="font-size: 18px;"></i> <!-- Increased Font Awesome icon size -->
                </button>
            </form>
        </div>

        <!-- Total Sales -->
        <div style="font-size: 15px; text-align: right; padding-right: 30px;">
            <span class="w3-bar-item">Total: <?php echo $totalSales; ?> Data Laba Rugi</span>
        </div>

        <div class="w3-responsive">
            <table class="w3-table-all w3-centered" border="1" style="border-collapse: collapse; width: 100%;">
                <tr class="w3-green">
                    <th>ID Hewan</th>
                    <th>HPP</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="barang-row">
                        <!-- Kolom ID Hewan -->
                        <td style="font-size: 15px;"><?php echo htmlspecialchars($row['id_hewan']); ?></td>

                        <!-- Kolom HPP -->
                        <td style="font-size: 15px;">Rp. <?php echo number_format($row['hpp'], 2, ',', '.'); ?></td>

                        <!-- Kolom Harga -->
                        <td style="font-size: 15px;">Rp. <?php echo number_format($row['harga'], 2, ',', '.'); ?></td>



                        <td style="font-size: 14px; text-align: center;">
                            <!-- Tombol Lihat Lainnya -->
                            <button
                                onclick="document.getElementById('detailModal<?= $row['id_hasil_labarugi'] ?>').style.display='block'"
                                class="w3-button w3-grey w3-round w3-small">
                                Lihat Lainnya
                            </button>
                            <a href="#"
                                onclick="deleteLabarugi('<?php echo htmlspecialchars($row['id_hasil_labarugi']); ?>', '<?php echo htmlspecialchars($row['id_hewan']); ?>')"
                                class="fa fa-trash w3-btn w3-button w3-round w3-red" style="font-size: 15px;"></a>
                        </td>
                    </tr>

                    <!-- Modal Detail -->
                    <div id="detailModal<?= $row['id_hasil_labarugi'] ?>" class="w3-modal">
                        <div class="w3-modal-content w3-card-4 w3-animate-top" style="max-width:600px">
                            <header class="w3-container w3-center w3-green">
                                <span
                                    onclick="document.getElementById('detailModal<?= $row['id_hasil_labarugi'] ?>').style.display='none'"
                                    class="w3-button w3-display-topright">&times;</span>
                                <h3>
                                    <b>Detail Laba Rugi</b>
                                </h3>
                            </header>
                            <div class="w3-container">
                                <p><strong>ID Laba Rugi:</strong> <?= htmlspecialchars($row['id_hasil_labarugi']); ?></p>
                                <p><strong>ID Hewan:</strong> <?= htmlspecialchars($row['id_hewan']); ?></p>
                                <p><strong>Jenis Kelamin:</strong> <?= htmlspecialchars($row['jenis_kelamin']); ?></p>
                                <p><strong>Jumlah:</strong> <?= htmlspecialchars($row['jumlah']); ?></p>
                                <p><strong>HPP (Harga Pokok Pembelian):</strong> <?= htmlspecialchars($row['hpp']); ?></p>
                                <p><strong>Harga:</strong> <?= htmlspecialchars($row['harga']); ?></p>
                                <p><strong>Tanggal Pembelian:</strong> <?= htmlspecialchars($row['tanggal_pembelian']); ?>
                                </p>
                                <p><strong>tanggal Penjualan:</strong> <?= htmlspecialchars($row['tanggal_penjualan']); ?>
                                </p>
                                <p><strong>Total Pakan:</strong> <?= htmlspecialchars($row['total_pakan']); ?></p>
                                <p><strong>Total Perawatan:</strong> <?= htmlspecialchars($row['total_perawatan']); ?></p>
                                <p><strong>Total Keuntungan:</strong> <?= htmlspecialchars($row['total_keuntungan']); ?></p>
                                <p><strong>Total Kerugian:</strong> <?= htmlspecialchars($row['total_kerugian']); ?></p>
                            </div>
                            <footer class="w3-container">
                                <button
                                    onclick="document.getElementById('detailModal<?= $row['id_hasil_labarugi'] ?>').style.display='none'"
                                    class="w3-button w3-red w3-right">Tutup</button>
                            </footer>
                        </div>
                    </div>
                <?php endwhile; ?>
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


        <!-- Modal for Deletion Confirmation -->
        <div id="deleteModal" class="w3-modal" style="align-items:center; padding-top: 15%;">
            <div class="w3-modal-content w3-animate-top w3-card-4">
                <header class="w3-container w3-red">
                    <span onclick="closeModal()" class="w3-button w3-display-topright">&times;</span>
                    <h2>Konfirmasi</h2>
                </header>
                <div class="w3-container">
                    <p id="modalMessage">Apakah Anda yakin ingin menghapus data ini?</p>
                    <div class="w3-right">
                        <button class="w3-button w3-grey" onclick="closeModal()">Batal</button>
                        <button class="w3-button w3-red" id="confirmDeleteButton">Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New User Button -->
        <a href="tambah_labarugi.php" class="w3-btn w3-round-xlarge w3-green bottom-right">
            <i class="fa fa-plus" style="font-size:30px;"></i>
        </a>


        <script>
            function w3_open() {
                document.getElementById("mySidebar").classList.add('show');
                document.getElementById("sidebarOverlay").classList.add('show');
            }

            function w3_close() {
                document.getElementById("mySidebar").classList.remove('show');
                document.getElementById("sidebarOverlay").classList.remove('show');
            }

            function deleteLabarugi(id_hasil_labarugi) {
                var modal = document.getElementById('deleteModal');
                modal.style.display = 'block'; // Display the delete confirmation modal

                var modalMessage = document.getElementById('modalMessage');
                modalMessage.textContent = "Apakah Anda yakin ingin menghapus data ini?";

                var confirmButton = document.getElementById('confirmDeleteButton');
                confirmButton.onclick = function () {
                    // Redirect to the PHP script for deletion
                    window.location.href = "hasil_labarugi.php?action=delete&id=" + encodeURIComponent(id_hasil_labarugi);
                };
            }

            function closeModal() {
                document.getElementById('deleteModal').style.display = 'none';
            }


            function searchItems() {
                let input = document.getElementById('searchInput').value.toLowerCase();
                let rows = document.querySelectorAll('.barang-row');

                rows.forEach(row => {
                    let rowText = row.innerText.toLowerCase();
                    if (rowText.includes(input)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

        </script>
</body>

</html>

<?php
mysqli_close($conn);
?>