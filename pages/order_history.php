<?php
    require '../database/connection.php';
    $cari_pesanan = '';
    $filter = isset($_POST['filter']) ? $_POST['filter'] : 'all'; // Default: semua pesanan
$cari_pesanan = isset($_POST['cari_pesanan']) ? $_POST['cari_pesanan'] : ''; // Default: tidak ada pencarian

    $sqlPesanan = "
        (
            SELECT 
                o.order_id AS id,
                u.username AS nama_pelanggan,
                p.product_name AS produk,
                oi.quantity AS jumlah,
                o.status AS status,
                o.total_amount AS jumlah_dibayar,
                'Online' AS metode_pembayaran,
                o.order_date AS tanggal
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN products p ON oi.product_id = p.product_id
            WHERE 
                (o.user_id LIKE :cari OR o.order_id LIKE :cari)
        )
        UNION ALL
        (
            SELECT 
                oo.offline_order_id AS id,
                oo.customer_name AS nama_pelanggan,
                p.product_name AS produk,
                ooi.quantity AS jumlah,
                oo.status AS status,
                oo.total_amount AS jumlah_dibayar,
                oo.payment_method AS metode_pembayaran,
                oo.order_date AS tanggal
            FROM offline_orders oo
            JOIN offline_order_items ooi ON oo.offline_order_id = ooi.offline_order_id
            JOIN products p ON ooi.product_id = p.product_id
            WHERE 
                (oo.customer_name LIKE :cari OR oo.offline_order_id LIKE :cari)
        )
    ";

    // Tambahkan kondisi filter
    if ($filter == 'online') {
        $sqlPesanan = "(
            SELECT 
                o.order_id AS id,
                u.username AS nama_pelanggan,
                p.product_name AS produk,
                oi.quantity AS jumlah,
                o.status AS status,
                o.total_amount AS jumlah_dibayar,
                'Online' AS metode_pembayaran,
                o.order_date AS tanggal
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN products p ON oi.product_id = p.product_id
            WHERE 
                (o.user_id LIKE :cari OR o.order_id LIKE :cari)
        )";
    } elseif ($filter == 'offline') {
        $sqlPesanan = "(
            SELECT 
                oo.offline_order_id AS id,
                oo.customer_name AS nama_pelanggan,
                p.product_name AS produk,
                ooi.quantity AS jumlah,
                oo.status AS status,
                oo.total_amount AS jumlah_dibayar,
                oo.payment_method AS metode_pembayaran,
                oo.order_date AS tanggal
            FROM offline_orders oo
            JOIN offline_order_items ooi ON oo.offline_order_id = ooi.offline_order_id
            JOIN products p ON ooi.product_id = p.product_id
            WHERE 
                (oo.customer_name LIKE :cari OR oo.offline_order_id LIKE :cari)
        )";
    }

    $sqlPesanan .= " ORDER BY tanggal DESC;";

    try {
        // Menjalankan query dengan prepared statement
        $stmt = $pdo->prepare($sqlPesanan);
        $stmt->execute([':cari' => "%$cari_pesanan%"]);
        $resultPesanan = $stmt->fetchAll(PDO::FETCH_ASSOC); // Ambil semua hasil
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Riwayat Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            margin-top: 50px;
        }
        h2, h3 {
            color: #800000;
        }
        .btn-custom {
            background-color: #800000;
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #8c5d65;
        }
        table {
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            text-align: left;
            padding: 12px;
        }
        th {
            background-color: #800000;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .form-control {
            border-radius: 8px;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-control, .btn-custom, table {
            border-color: #800000;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Kelola Riwayat Pesanan</h2>
    <div class="form-section">
    <h3>Cari Riwayat Pesanan</h3>
    <form method="post" class="d-flex">
        <input type="text" name="cari_pesanan" class="form-control me-2" placeholder="Cari riwayat..." value="<?php echo htmlspecialchars($cari_pesanan); ?>">
        <select name="filter" class="form-select me-2">
            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Semua</option>
            <option value="online" <?php echo $filter === 'online' ? 'selected' : ''; ?>>Online</option>
            <option value="offline" <?php echo $filter === 'offline' ? 'selected' : ''; ?>>Offline</option>
        </select>
        <button type="submit" name="cari" class="btn btn-custom">Cari</button>
    </form>
</div>


<div class="form-section">
    <h3>Daftar Riwayat Pesanan</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Pelanggan</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Jumlah Dibayar</th>
                <th>Metode Pembayaran</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($resultPesanan)): ?>
                <?php foreach ($resultPesanan as $rowPesanan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rowPesanan['id']); ?></td>
                        <td><?php echo htmlspecialchars($rowPesanan['nama_pelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($rowPesanan['produk']); ?></td>
                        <td><?php echo htmlspecialchars($rowPesanan['jumlah']); ?></td>
                        <td><?php echo htmlspecialchars($rowPesanan['status']); ?></td>
                        <td><?php echo number_format($rowPesanan['jumlah_dibayar'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($rowPesanan['metode_pembayaran']); ?></td>
                        <td><?php echo htmlspecialchars($rowPesanan['tanggal']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">Tidak ada riwayat pesanan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
