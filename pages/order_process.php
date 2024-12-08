<?php
    session_start();
    include '../database/connection.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Ambil data dari sesi dan formulir
            $userId = $_SESSION['user_id'];
            $selectedProducts = json_decode($_POST['selected_products'], true);
            $totalAmount = floatval(str_replace('.', '', $_POST['total_amount']));
            $orderDate = date('Y-m-d');

            // Cek apakah file bukti pembayaran ada
            $paymentImage = null;
            if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
                // Tentukan direktori dan nama file
                $uploadDir = '../assets/images/';
                $fileName = uniqid() . '_' . basename($_FILES['proof_of_payment']['name']);
                $targetFile = $uploadDir . $fileName;

                // Validasi ekstensi file
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                    throw new Exception("Format file tidak valid. Hanya JPG, JPEG, atau PNG yang diperbolehkan.");
                }

                // Pindahkan file ke folder tujuan
                if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $targetFile)) {
                    $paymentImage = $fileName; // Nama file yang akan disimpan di database
                } else {
                    throw new Exception("Gagal mengunggah bukti pembayaran.");
                }
            } else {
                throw new Exception("File bukti pembayaran tidak ditemukan atau terdapat kesalahan.");
            }

            // Simpan data pesanan ke database
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, order_date, payment_image) 
                                VALUES (:user_id, :total_amount, 'Pending', :order_date, :payment_image)");
            $stmt->execute([
                ':user_id' => $userId,
                ':total_amount' => $totalAmount,
                ':order_date' => $orderDate,
                ':payment_image' => $paymentImage,
            ]);
            $orderId = $pdo->lastInsertId();

            // Simpan item pesanan
            foreach ($selectedProducts as $product) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) 
                                    VALUES (:order_id, :product_id, :quantity)");
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $product['cart_id'], // Assuming 'cart_id' is product_id
                    ':quantity' => $product['quantity'],
                ]);
            }

            // Redirect ke halaman sukses
            header("Location: order_success.php");
            exit();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
?>