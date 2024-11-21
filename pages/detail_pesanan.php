<?php
    require '../database/connection.php';

    // Mendapatkan ID pesanan dan order_id dari parameter URL
    $order_id = isset($_GET['id']) ? $_GET['id'] : '';
    $order_id_from_url = isset($_GET['order_id']) ? $_GET['order_id'] : '';

    if (empty($order_id)) {
        die("ID pesanan tidak ditemukan.");
    }

    // Cek apakah ID pesanan berasal dari pesanan online atau offline
    // Cek apakah ID berasal dari pesanan online
    $sqlCheckPesananOnline = "SELECT COUNT(*) FROM orders WHERE order_id = :order_id";
    $stmtCheckOnline = $pdo->prepare($sqlCheckPesananOnline);
    $stmtCheckOnline->execute([':order_id' => $order_id]);
    $orderExistOnline = $stmtCheckOnline->fetchColumn();

    // Jika pesanan online ditemukan, ambil detailnya
    if ($orderExistOnline) {
        $sqlDetailPesanan = "
            SELECT 
                oi.product_id AS id_produk,
                p.product_name AS produk,
                oi.quantity AS jumlah,
                p.price AS harga,
                (oi.quantity * p.price) AS total_harga
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = :order_id
        ";
    } else {
        // Jika pesanan online tidak ditemukan, cek pesanan offline
        $sqlCheckPesananOffline = "SELECT COUNT(*) FROM offline_orders WHERE offline_order_id = :order_id";
        $stmtCheckOffline = $pdo->prepare($sqlCheckPesananOffline);
        $stmtCheckOffline->execute([':order_id' => $order_id_from_url]);
        $orderExistOffline = $stmtCheckOffline->fetchColumn();

        // Jika pesanan offline ditemukan, ambil detailnya
        if ($orderExistOffline) {
            $sqlDetailPesanan = "
                SELECT 
                    ooi.product_id AS id_produk,
                    p.product_name AS produk,
                    ooi.quantity AS jumlah,
                    p.price AS harga,
                    (ooi.quantity * p.price) AS total_harga
                FROM offline_order_items ooi
                JOIN products p ON ooi.product_id = p.product_id
                WHERE ooi.offline_order_id = :order_id
            ";
        } else {
            die("Pesanan tidak ditemukan.");
        }
    }

    try {
        $stmt = $pdo->prepare($sqlDetailPesanan);
        $stmt->execute([':order_id' => $order_id_from_url]);
        $resultDetailPesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h2>Detail Pesanan</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($resultDetailPesanan)): ?>
                <?php foreach ($resultDetailPesanan as $rowDetail): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rowDetail['produk']); ?></td>
                        <td><?php echo htmlspecialchars($rowDetail['jumlah']); ?></td>
                        <td><?php echo number_format($rowDetail['harga'], 2, ',', '.'); ?></td>
                        <td><?php echo number_format($rowDetail['total_harga'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Tidak ada detail pesanan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="kelola_pesanan.php" class="btn btn-secondary">Kembali</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
