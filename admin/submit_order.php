<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require '../database/connection.php';

        try {
            $customerName = $_POST['customer_name'];
            $orderDate = $_POST['order_date'];
            $paymentMethod = $_POST['payment_method'];
            $totalAmount = $_POST['total_amount'];
            $orderItems = json_decode($_POST['order_items'], true);

            // Mulai transaksi
            $pdo->beginTransaction();

            // Insert ke tabel offline_orders
            $stmt = $pdo->prepare("INSERT INTO offline_orders (customer_name, total_amount, status, order_date, payment_method) VALUES (?, ?, 'Completed', ?, ?)");
            $stmt->execute([$customerName, $totalAmount, $orderDate, $paymentMethod]);
            $offlineOrderId = $pdo->lastInsertId();

            // Insert ke tabel offline_order_items
            $stmtItem = $pdo->prepare("INSERT INTO offline_order_items (offline_order_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");

            foreach ($orderItems as $item) {
                // Ambil product_id berdasarkan nama produk
                $stmtProduct = $pdo->prepare("SELECT product_id, stock FROM products WHERE product_name = ?");
                $stmtProduct->execute([$item['productName']]);
                $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $productId = $product['product_id'];
                    $currentStock = $product['stock'];

                    // Validasi stok cukup
                    if ($item['quantity'] > $currentStock) {
                        throw new Exception("Stok produk '{$item['productName']}' tidak mencukupi untuk jumlah yang dipesan.");
                    }

                    // Masukkan ke tabel offline_order_items
                    $stmtItem->execute([$offlineOrderId, $productId, $item['quantity']]);

                    // Kurangi stok di tabel products
                    $stmtUpdateStock->execute([$item['quantity'], $productId]);
                } else {
                    throw new Exception("Produk '{$item['productName']}' tidak ditemukan di database.");
                }
            }

            // Commit transaksi
            $pdo->commit();
            echo 'success';
        } catch (Exception $e) {
            $pdo->rollBack();
            echo 'Error: ' . $e->getMessage();
        }
    }
?>
