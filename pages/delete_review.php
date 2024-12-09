<?php
    session_start();
    include '../database/connection.php';

    if (isset($_GET['review_id']) && isset($_GET['product_id']) && isset($_SESSION['user_id'])) {
        $review_id = $_GET['review_id'];
        $product_id = $_GET['product_id'];
        $user_id = $_SESSION['user_id'];

        // Check if the review belongs to the logged-in user
        $query = "SELECT user_id FROM reviews WHERE review_id = :review_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
        $stmt->execute();
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($review && $review['user_id'] == $user_id) {
            // Delete the review
            $delete_query = "DELETE FROM reviews WHERE review_id = :review_id";
            $delete_stmt = $pdo->prepare($delete_query);
            $delete_stmt->bindParam(':review_id', $review_id, PDO::PARAM_INT);
            if ($delete_stmt->execute()) {
                header("Location: product_detail.php?product_id=" . $product_id); // Redirect back to the product detail page
            } else {
                echo "Error deleting review.";
            }
        } else {
            echo "You do not have permission to delete this review.";
        }
    } else {
        echo "Invalid request.";
    }
?>
