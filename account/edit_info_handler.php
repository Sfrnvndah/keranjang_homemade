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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    try {
        // Cek apakah email sudah digunakan oleh user lain
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :userId");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Email sudah terdaftar!';
            header('Location: ../account/account.php');
            exit;
        }

        // Update informasi user
        $stmt = $pdo->prepare("
            UPDATE users 
            SET name = :name, email = :email, phone = :phone, address = :address
            WHERE user_id = :userId
        ");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['message'] = 'Informasi berhasil diperbarui!';
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