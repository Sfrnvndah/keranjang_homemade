<?php
    // Session
    if (!isset($_SESSION)) {
        session_name('admin_session');
        session_start();
    }

    require '../database/connection.php';

    // Cek apakah admin sudah login
    if (!isset($_SESSION['admin_user_id'])) {
        // Redirect ke halaman login jika belum login
        header("Location: form/admin_login.php");
        exit;
    }

    // Ambil ID admin dari session
    $adminId = $_SESSION['admin_user_id'];

    // Cek apakah form disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari form
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);

        // Validasi input
        if (empty($username) || empty($email) || empty($name) || empty($phone)) {
            echo "Harap isi semua kolom yang diperlukan.";
            exit;
        }

        try {
            // Update data admin di database
            $stmt = $pdo->prepare("
                UPDATE users 
                SET 
                    username = :username, 
                    email = :email, 
                    password = :password, -- Langsung simpan teks biasa
                    name = :name, 
                    phone = :phone 
                WHERE user_id = :user_id AND level = 1
            ");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $password, // Password teks biasa
                ':name' => $name,
                ':phone' => $phone,
                ':user_id' => $adminId
            ]);

            // Redirect kembali ke halaman profil dengan pesan sukses
            header("Location: profile.php?status=success");
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    } else {
        echo "Metode HTTP tidak valid.";
        exit;
    }