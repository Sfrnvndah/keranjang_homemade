<?php
    session_start();
    include '../database/connection.php';

    // Periksa apakah user sudah login
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Anda belum login.']);
        exit;
    }

    // Ambil data dari form
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $trainingListId = isset($_POST['training_list_id']) ? $_POST['training_list_id'] : null;

    // Validasi input
    if (!$userId || !$trainingListId) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    try {
        // Periksa apakah user sudah mendaftar pada pelatihan ini
        $stmt = $pdo->prepare("SELECT * FROM online_training_participants WHERE user_id = ? AND training_list_id = ?");
        $stmt->execute([$userId, $trainingListId]);
        $existingRegistration = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRegistration) {
            echo json_encode(['status' => 'error', 'message' => 'Anda sudah mendaftar pada pelatihan ini.']);
            exit;
        }

        // Simpan data pendaftaran ke database
        $stmt = $pdo->prepare("INSERT INTO online_training_participants (training_list_id, user_id) VALUES (?, ?)");
        $stmt->execute([$trainingListId, $userId]);

        echo json_encode(['status' => 'success', 'message' => 'Pendaftaran berhasil.']);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        exit;
    }
?>
