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
                    $sql = "INSERT INTO users (username, email, password, level) VALUES (:username, :email, :password, :level)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password' => $password,
                        ':level' => 1
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
        <title>Register Admin</title>
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/register-admin.css">
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
    </head>

    <body>
        <div class="form-container">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Admin Registration</h3>
                </div>
                <div class="card-body">
                    <form action="admin_register.php" method="post">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>    
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>    
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" class="form-control">
                        </div>    
                        <div class="form-group">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                        <div class="form-group text-center">
                            <p>Sudah punya akun? <a href="admin_login.php">Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <p id="popupMessage"><?php echo $message; ?></p>
                <button onclick="closePopup()">Close</button>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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