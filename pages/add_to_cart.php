<?php
    session_start();
    include '../database/connection.php';
    $response = ['success' => false, 'message' => ''];
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = 'Silahkan login terlebih dahulu.';
        echo json_encode($response);
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    if ($product_id <= 0 || $quantity <= 0) {
        $response['message'] = 'ID produk atau jumlah tidak valid.';
        echo json_encode($response);
        exit;
    }
    try {
        $query = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cartItem) {
            // Perbarui jumlah jika item sudah ada di keranjang
            $newQuantity = $cartItem['quantity'] + $quantity;
            $updateQuery = "UPDATE cart SET quantity = :quantity, added_at = NOW() WHERE cart_id = :cart_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':quantity', $newQuantity, PDO::PARAM_INT);
            $updateStmt->bindParam(':cart_id', $cartItem['cart_id'], PDO::PARAM_INT);
            $updateStmt->execute();
        } else {
            // Tambahkan produk baru ke keranjang
            $insertQuery = "INSERT INTO cart (user_id, product_id, quantity, added_at) VALUES (:user_id, :product_id, :quantity, NOW())";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insertStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $insertStmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $insertStmt->execute();
        }
        $response['success'] = true;
        $response['message'] = 'Produk berhasil ditambahkan ke keranjang.';
    } catch (Exception $e) {
        $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
    echo json_encode($response);
?>
