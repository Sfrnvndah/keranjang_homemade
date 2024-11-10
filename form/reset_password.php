<?php
    require '../database/connection.php';
    $message = "";
    $popupMessage = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];
        $email = $_POST["email"];
        $token = $_POST["token"];
        $expire = $_POST["expire"];
        if ($password !== $confirm_password) {
            $message = "Password tidak sama!";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            if ($user) {
                $plainPassword = $password;
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
                $stmt->execute([':password' => $plainPassword, ':email' => $email]);
                $popupMessage = "Password berhasil diubah!";
            } else {
                $message = "Email tidak valid atau tidak ditemukan!";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.css">
    <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
    <link rel="stylesheet" href="../assets/css/forgot_password.css">
    <link rel="stylesheet" href="../assets/css/popup.css">
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-form">
            <h2>Reset Password</h2>
            <?php if ($message): ?>
                <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="reset_password.php?email=<?php echo urlencode($_GET['email'] ?? $_POST['email'] ?? ''); ?>&token=<?php echo urlencode($_GET['token'] ?? $_POST['token'] ?? ''); ?>&expire=<?php echo urlencode($_GET['expire'] ?? $_POST['expire'] ?? ''); ?>" method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? $_POST['email'] ?? ''); ?>">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? $_POST['token'] ?? ''); ?>">
                <input type="hidden" name="expire" value="<?php echo htmlspecialchars($_GET['expire'] ?? $_POST['expire'] ?? ''); ?>">
                <input type="password" name="password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
                <div class="back-to-login">
                    <p><a href="login.php">Back to Login</a></p>
                </div>
            </form>
        </div>
    </div>
    <?php if ($popupMessage): ?>
        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <p id="popupMessage"><?php echo $popupMessage; ?></p>
                <button onclick="closePopup()">Close</button>
            </div>
        </div>
    <?php endif; ?>
    <script src="../assets/js/jquery-2.1.0.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        function showPopup(message) {
            document.getElementById("popupMessage").innerText = message;
            document.getElementById("popupOverlay").style.display = "block";
        }

        // Function untuk menutup popup
        function closePopup() {
            document.getElementById("popupOverlay").style.display = "none";
        }

        <?php if (!empty($popupMessage)): ?>
            showPopup("<?php echo $popupMessage; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
