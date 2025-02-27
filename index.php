<?php
session_start();
include 'koneksi.php';

// Check if user is already logged in
if (isset($_SESSION["username"])) {
    echo "<script> window.location.href='daftar_hewan.php' </script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="logo e-mbek.png">
</head>
<style>
    .password-field {
        border: none;
        /* Remove all borders */
        border-bottom: 1px solid #ccc;
        /* Add bottom border */
        border-radius: 0;
        /* Remove rounded corners */
        padding-right: 30px;
        /* Space for the icon */
        background: none;
        /* Transparent background */
        box-shadow: none;
        /* Remove shadow */
    }

    .password-field:focus {
        border-bottom: 1px solid #000;
        /* Highlighted bottom border on focus */
        outline: none;
        /* Remove focus outline */
    }

    .w3-input-container {
        margin-top: 20px;
        position: relative;
        /* Ensure relative positioning */
    }

    i.fa-eye,
    i.fa-eye-slash {
        position: absolute;
        /* Absolute positioning for the icon */
        right: 10px;
        /* Right margin for positioning */
        top: 35px;
        /* Adjust to align with input */
        cursor: pointer;
        /* Cursor pointer for better UX */
        z-index: 1;
        /* Higher z-index to ensure clickability */
    }
</style>

<body class="w3-green">
    <div class="w3-center">
        <img src="logo e-mbek.png" alt="e-mbek" style="vertical-align: middle; width: 250px;">
    </div>

    <form class="w3-container w3-card-4 w3-light-grey w3-padding-16 w3-margin" action="" method="post"
        style="border-radius: 10px;">
        <h2 class="w3-container w3-center"><b>Login</b></h2>
        <label for="username">Username</label>
        <input type="text" class="w3-input w3-light-grey" name="username" required>

        <!-- Password Field -->
        <div class="w3-input-container" style="position: relative; margin-bottom: 30px;">
            <label for="password">Password</label>
            <input type="password" id="password" class="w3-input w3-border-bottom w3-light-grey password-field"
                name="password" required>
            <!-- Eye icon inside the input field -->
            <i id="togglePassword" class="fa fa-eye"
                style="position: absolute; right: 10px; top: 35px; cursor: pointer; z-index: 1;"></i>
        </div>

        <button type="submit" name="submit" value="Login"
            class="w3-input w3-button w3-round-large w3-green w3-margin-top">Login</button>
        <a href="register.php" class="w3-input w3-button w3-round-large w3-grey w3-margin-top">Register</a>
    </form>

    <!-- Modal for displaying notification -->
    <div class="w3-container">
        <!-- Modal for failed login -->
        <div id="idgagal" class="w3-modal" onclick="closeModal(event)">
            <div class="w3-modal-content w3-animate-top w3-card-4">
                <header class="w3-container w3-red">
                    <span onclick="document.getElementById('idgagal').style.display='none'"
                        class="w3-button w3-display-topright">&times;</span>
                    <h2>Informasi</h2>
                </header>
                <div class="w3-container">
                    <p style="color: black;">Login Gagal, Check Username dan Password</p>
                </div>
            </div>
        </div>

        <!-- Modal for unconfirmed users -->
        <div id="idUnconfirmed" class="w3-modal" onclick="closeModal(event)">
            <div class="w3-modal-content w3-animate-top w3-card-4">
                <header class="w3-container w3-yellow">
                    <span onclick="document.getElementById('idUnconfirmed').style.display='none'"
                        class="w3-button w3-display-topright">&times;</span>
                    <h2>Informasi</h2>
                </header>
                <div class="w3-container">
                    <p style="color: black;">Anda belum dikonfirmasi. Silakan tunggu konfirmasi dari admin.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to close modal if click event occurs outside the modal content
        function closeModal(event) {
            var modal = document.getElementById('idgagal');
            var unconfirmedModal = document.getElementById('idUnconfirmed');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
            if (event.target === unconfirmedModal) {
                unconfirmedModal.style.display = 'none';
            }
        }
        // Toggle Password Visibility for Password Field
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.classList.replace('fa-eye', 'fa-eye-slash'); // Change icon to "eye-slash"
            } else {
                passwordField.type = 'password';
                this.classList.replace('fa-eye-slash', 'fa-eye'); // Change icon back to "eye"
            }
        });
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Escape user inputs to prevent SQL injection
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Query to check if username, password, and confirmation status are valid
        $sql = "SELECT COUNT(*) AS jumlah FROM mbek_pengguna WHERE username='$username' AND password='$password'";
        $result = mysqli_query($conn, $sql);
        $jumlahData = 0;

        if ($row = mysqli_fetch_assoc($result)) {
            $jumlahData = $row['jumlah'];
        }

        // Query to check confirmation status
        $sqlConfirmasi = "SELECT konfirmasi FROM mbek_pengguna WHERE username='$username' AND password='$password'";
        $resultConfirmasi = mysqli_query($conn, $sqlConfirmasi);

        $konfirmasi = 0;
        if ($rowConfirmasi = mysqli_fetch_assoc($resultConfirmasi)) {
            $konfirmasi = $rowConfirmasi['konfirmasi'];
        }

        if ($jumlahData == 1) {
            if ($konfirmasi == 1) {
                $_SESSION["username"] = $username;
                echo "<script> window.location.href='daftar_hewan.php' </script>";
            } else {
                // User is not confirmed
                session_destroy();
                ?>
                <script>
                    document.getElementById('idUnconfirmed').style.display = 'block';
                </script>
                <?php
            }
        } else {
            // Failed Login
            session_destroy();
            ?>
            <script>
                document.getElementById('idgagal').style.display = 'block';
            </script>
            <?php
        }
    }

    $conn->close();
    ?>

    <script>
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