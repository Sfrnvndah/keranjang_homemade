<?php
    // Mulai session
    session_start();

    // Hubungkan ke database
    require '../database/connection.php';

    // Inisialisasi pesan
    $message = "";

    // Jika form disubmit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Ambil data dari form
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            // Query untuk mengambil data admin
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND level = '1'");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            // Validasi password
            if ($user && $password == $user['password']) {
                // Set session untuk admin
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['level'] = $user['level'];

                // Redirect ke dashboard admin
                header("Location: ../admin.php");
                exit;
            } else {
                $message = "Username atau password salah atau Anda bukan admin!";
            }
        } catch (PDOException $e) {
            $message = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.css">
    <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/popup.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2>ADMIN LOGIN</h2>
            <form action="admin_login.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                <p style="color: #00827f;">.</p>
                <p><a href="forgot_password.php">Forgot Password?</a></p>
            </form>
            <div class="register-link">
                <p>Back to <a href="../index.php">Home</a></p>
            </div>
        </div>
    </div>

    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-content">
            <p id="popupMessage"></p>
            <button onclick="closePopup()">Close</button>
        </div>
    </div>
    <script src="../assets/js/jquery-2.1.0.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        function showPopup(message) {
            document.getElementById("popupMessage").innerText = message;
            document.getElementById("popupOverlay").style.display = "block";
        }
        function closePopup() {
            document.getElementById("popupOverlay").style.display = "none";
        }
        <?php if ($message): ?>
            showPopup("<?php echo $message; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
