<?php
    require '../database/connection.php';

    if (isset($_POST['product_name'])) {
        $productName = $_POST['product_name'];
    
        // Query untuk mengambil stok dan harga produk berdasarkan nama
        $stmt = $pdo->prepare("SELECT stock, price FROM products WHERE product_name = :product_name LIMIT 1");
        $stmt->execute(['product_name' => $productName]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($product) {
            echo json_encode(['success' => true, 'stock' => $product['stock'], 'price' => $product['price']]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
?>