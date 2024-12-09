<?php
    include ('../database/connection.php');

    if (isset($_GET['id'])) {
        $productId = intval($_GET['id']);
        try {
            $query = "DELETE FROM products WHERE product_id = :product_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            header("Location: product.php?status=success&message=" . urlencode("Produk berhasil dihapus"));
            exit;
        } catch (PDOException $e) {
            header("Location: product.php?status=error&message=" . urlencode("Terjadi kesalahan saat menghapus produk"));
            exit;
        }
    } else {
        header("Location: product.php?status=error&message=" . urlencode("ID produk tidak valid"));
        exit;
    }