<?php
include 'koneksi.php'; // Sertakan file koneksi ke database

session_start(); // Mulai sesi untuk mengakses informasi sesi pengguna

// Pastikan pengguna telah login sebelumnya
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect jika pengguna belum login
    exit;
}

$username = $_SESSION['username']; // Ambil username pengguna dari sesi

// Query untuk mengambil data pengguna berdasarkan username
$query = "SELECT * FROM mbek_pengguna WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Ambil data pengguna
if (mysqli_num_rows($result) > 0) {
    $mbek_pengguna = mysqli_fetch_assoc($result);
    $user_record = $mbek_pengguna['username']; // Ambil nilai username dari pengguna
} else {
    // Handle jika tidak ada data pengguna yang ditemukan
    $user_record = ''; // Atau sesuaikan dengan logika penanganan kesalahan
}

// Handle pencarian
$search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// **Perbaikan Query SQL**
$sql_count = "SELECT COUNT(username) AS total FROM mbek_pengguna 
              WHERE username != 'admin' AND (username LIKE '%$search_keyword%' OR nama LIKE '%$search_keyword%')";
$result_count = mysqli_query($conn, $sql_count);

if (!$result_count) {
    die("Query error: " . mysqli_error($conn));
}

$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];

// Jumlah pengguna per halaman
$records_per_page = 10;

// Menghitung jumlah halaman
$total_pages = ceil($total_records / $records_per_page);

// Mendapatkan halaman saat ini
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Menghitung offset untuk query SQL
$offset = ($page - 1) * $records_per_page;

// Query untuk mengambil data pengguna dengan pencarian dan pagination
$sql = "SELECT * FROM mbek_pengguna 
        WHERE username != 'admin' AND (username LIKE '%$search_keyword%' OR nama LIKE '%$search_keyword%') 
        ORDER BY username DESC 
        LIMIT $offset, $records_per_page";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

// Query untuk mendapatkan total jumlah pengguna (tidak termasuk admin)
$totalQuery = "SELECT COUNT(*) AS total_users FROM mbek_pengguna WHERE username != 'admin'";
$totalResult = mysqli_query($conn, $totalQuery);

if ($totalResult) {
    $totalData = mysqli_fetch_assoc($totalResult);
    $totalUsers = $totalData['total_users'];
} else {
    $totalUsers = 0; // Fallback jika terjadi kesalahan
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
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
        <div class="w3-green" style="display: flex; align-items: center; padding: 10px;">
            <button class="w3-button w3-xlarge" onclick="w3_open()">☰</button>
            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
                <h3
                    style="margin: 0; line-height: 1.5rem; text-align: center; font-size: 25px; margin-top:5px; margin-bottom: 10px;">
                    <b>Daftar Pengguna</b>
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
                    <p>Apakah Anda yakin ingin menghapus pengguna ini?</p>
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
                <!-- Input Field with Modern Style -->
                <input type="text" name="search" class="w3-input w3-border" placeholder="Cari username..."
                    value="<?php echo isset($_GET['search']) && !empty($_GET['search']) ? '' : htmlspecialchars($search_keyword); ?>"
                    style="width: 100%; padding: 12px 20px; padding-right: 60px; border-radius: 50px; border: 2px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); font-size: 16px;">

                <!-- Modern "Cari" Button -->
                <button type="submit" class="w3-button w3-green"
                    style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); height: 40px; width: 40px; border-radius: 50%; border: none; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-search" style="font-size: 18px;"></i> <!-- Increased Font Awesome icon size -->
                </button>
            </form>
        </div>

        <!-- Total users -->
        <div style="font-size: 15px; text-align: right; padding-right: 30px;">
            <span class="w3-bar-item">Total: <?php echo $totalUsers; ?> pengguna</span>
        </div>

        <!-- Table of Users -->
        <div class="w3-responsive">
            <table class="w3-table-all w3-centered" border="1" style="border-collapse: collapse; width: 100%;">
                <tr class="w3-green">
                    <th>Username</th>
                    <th>Nama</th>
                    <?php if ($user_record === 'admin') { ?>
                        <th>Aksi</th>
                    <?php } ?>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr class="username-row">
                        <td style="font-size: 15px;"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td style="font-size: 15px;"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <?php if ($user_record === 'admin') { ?>
                            <td style="font-size: 14px; text-align: center;">
                                <a href="edit_pengguna.php?username=<?php echo $row['username']; ?>"
                                    class="material-icons w3-yellow w3-btn w3-button w3-round"
                                    style="font-size: 15px;">&#xe22b;</a>
                            <?php } ?>
                            <?php if ($user_record === 'admin') { ?>
                                <a href="#"
                                    onclick="deleteUser('<?php echo htmlspecialchars($row['username']); ?>', '<?php echo htmlspecialchars($row['nama']); ?>')"
                                    class="fa fa-trash w3-btn w3-button w3-round w3-red" style="font-size: 15px;"></a>
                            <?php } ?>
                        </td>
                    </tr>
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

        <!-- Add New User Button -->
        <?php if ($user_record === 'admin') { ?>
            <a href="tambah_pengguna.php" class="w3-btn w3-round-xlarge w3-green bottom-right">
                <i class="fa fa-plus" style="font-size:30px"></i>
            </a>
        <?php } ?>


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

            function deleteUser(username, nama) {
                var modal = document.getElementById('deleteModal');
                modal.style.display = 'block'; // Display the delete confirmation modal
                var modalMessage = modal.querySelector('p');
                modalMessage.textContent = "Apakah Anda yakin ingin menghapus pengguna '" + nama + "'?";
                var confirmButton = modal.querySelector('.w3-button.w3-red');
                confirmButton.onclick = function () {
                    window.location.href = "hapus_pengguna.php?action=delete&username=" + encodeURIComponent(username);
                };
            }

            function searchItems() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("searchInput");
                filter = input.value.toUpperCase();
                table = document.querySelector("table");
                tr = table.getElementsByClassName("username-row");

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