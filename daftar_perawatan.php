<?php
include "koneksi.php";

// Ambil data dari database
$result = $conn->query("SELECT * FROM mbek_perawatan ORDER BY date_record DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Perawatan</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>

<body>
    <div class="w3-container">
        <h2>Daftar Perawatan</h2>
        <table class="w3-table-all w3-hoverable">
            <thead>
                <tr class="w3-light-grey">
                    <th>Jenis Perawatan</th>
                    <th>Harga</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["jenis_pakan"]); ?></td>
                        <td>Rp. <?php echo number_format($row["harga_perawatan"], 2, ',', '.'); ?></td>
                        <td><?php echo $row["tanggal"]; ?></td>
                        <td>
                            <a class="w3-button w3-blue"
                                href="edit_perawatan.php?jenis_pakan=<?php echo urlencode($row['jenis_pakan']); ?>">Edit</a>
                            <form method="POST" action="hapus_perawatan.php" style="display:inline;">
                                <input type="hidden" name="jenis_pakan"
                                    value="<?php echo htmlspecialchars($row['jenis_pakan']); ?>">
                                <button class="w3-button w3-red" onclick="return confirm('Hapus data ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="tambah_perawatan.php" class="w3-button w3-green">Tambah Data</a>
    </div>
</body>

</html>