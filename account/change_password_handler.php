<?php
    session_start();
    require '../database/connection.php';

    // Cek apakah user sudah login
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        // Jika tidak login, redirect ke halaman login
        header('Location: ../form/login.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari form
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validasi password
        if ($newPassword !== $confirmPassword) {
            $_SESSION['message'] = 'Password dan konfirmasi password tidak cocok!';
            header('Location: ../account/account.php');
            exit;
        }

        try {
            // Update password di database (langsung teks biasa)
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :userId");
            $stmt->bindParam(':password', $newPassword, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['message'] = 'Password berhasil diperbarui!';
            header('Location: ../account/account.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            header('Location: ../account/account.php');
            exit;
        }
    } else {
        // Jika request bukan POST, redirect ke halaman akun
        header('Location: ../account/account.php');
        exit;
    }
?>