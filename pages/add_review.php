<?php
session_start();
include '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $product_id = $_POST['product_id'] ?? null;
    $review_text = $_POST['review_text'] ?? null;
    $rating = $_POST['rating'] ?? null;

    if ($user_id && $product_id && $review_text && $rating) {
        $query = "INSERT INTO reviews (product_id, user_id, review_text, rating) 
                  VALUES (:product_id, :user_id, :review_text, :rating)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("Location: product_detail.php?product_id=$product_id");
        } else {
            echo "Gagal menyimpan ulasan.";
        }
    } else {
        echo "Data tidak lengkap.";
    }
} else {
    echo "Invalid request.";
}
?>
