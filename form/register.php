<?php
    include '../database/connection.php';
    $message = "";
    $username = $email = $password = $confirm_password = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        if ($password != $confirm_password) {
            $message = "Passwords tidak sama!";
        } else {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existingUser) {
                $message = "Email sudah terdaftar. Silahkan gunakan email yang berbeda.";
            } else {
                try {
                    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password' => $password
                    ]);
                    $message = "Pendaftaran berhasil!";
                    $username = $email = $password = $confirm_password = "";
                } catch (PDOException $e) {
                    $message = "Error: " . $e->getMessage();
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register | Pak Tara</title>
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/register.css">
        <link rel="stylesheet" href="../assets/css/popup.css">
    </head>
    <body>
        <div class="register-container">
            <div class="register-form">
                <h2>REGISTER</h2>
                <form action="register.php" method="post">
                    <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="submit">Register</button>
                </form>
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <p id="popupMessage"><?php echo $message; ?></p>
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