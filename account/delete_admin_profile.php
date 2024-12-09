<?php
    if (!isset($_SESSION)) {
        session_name('admin_session');
        session_start();
    }
    require '../database/connection.php';

    // Cek apakah admin sudah login
    if (!isset($_SESSION['admin_user_id'])) {
        // Redirect ke halaman login jika belum login
        header("Location: ../form/admin_login.php");
        exit;
    }

    $adminId = $_SESSION['admin_user_id'];

    // Cek jika admin yang sedang login mencoba menghapus dirinya sendiri
    if ($adminId) {
        try {
            // Hapus akun admin
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id AND level = 1");
            $stmt->execute([':user_id' => $adminId]);

            // Hapus session dan arahkan ke halaman login dengan pesan sukses
            session_destroy();
            header("Location: ../form/admin_login.php?status=deleted");
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    } else {
        echo "Admin tidak ditemukan.";
        exit;
}