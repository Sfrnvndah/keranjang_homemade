<?php
    if (!isset($_SESSION)) {
        session_name('admin_session');
        session_start();
    }
    require '../database/connection.php';
    $message = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND level = 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        if ($user) {
            $resetLink = "localhost/keranjang_homemade/form/admin_reset_password.php?email=" . urlencode($email);
            $subject = "Password Reset Request";
            $message = "Klik link berikut untuk mereset password Anda: " . $resetLink;
            if (mail($email, $subject, $message)) {
                $message = "Link untuk reset password sudah dikirimkan ke email " . htmlspecialchars($email) . ".";
            } else {
                $message = "Terjadi kesalahan saat mengirim email. Coba lagi.";
            }
        } else {
            $message = "Email tidak terdaftar.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Forgot Password Admin</title>
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/login-admin.css">
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
    </head>
    <body>
        <div class="login-container">
            <div class="login-form">
                <h2>Lupa Password</h2>
                <form action="admin_forgot_password.php" method="post">
                    <input type="text" name="email" placeholder="Enter your Email" required>
                    <button type="submit">Send Reset Link</button>
                    <p style="text-align: center;">
                        <a href="admin_login.php">Back to Login</a>
                    </p>
                </form>
            </div>
        </div>

        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <p id="popupMessage"><?php echo htmlspecialchars($message); ?></p>
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
            <?php if (!empty($message)): ?>
                showPopup("<?php echo $message; ?>");
            <?php endif; ?>
        </script>
    </body>
</html>