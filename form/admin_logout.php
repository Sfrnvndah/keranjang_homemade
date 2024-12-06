<?php
    // Pastikan session untuk admin dimulai
    if (!isset($_SESSION)) {
        session_name('admin_session');
        session_start();
    }

    // Hapus semua data session dan destroy session
    session_unset(); // Menghapus semua data session
    session_destroy(); // Menghapus session dari server

    // Redirect ke halaman login
    header("Location: admin_login.php");
    exit;
?>