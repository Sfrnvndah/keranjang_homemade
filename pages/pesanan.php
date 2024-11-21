<?php
    require '../database/connection.php';
    $cari_pesanan = '';
    $filter = isset($_POST['filter']) ? $_POST['filter'] : 'all'; // Default: semua pesanan
    $cari_pesanan = isset($_POST['cari_pesanan']) ? $_POST['cari_pesanan'] : ''; // Default: tidak ada pencarian

    // Query Pesanan berdasarkan status 'Pending'
    $sqlPesanan = "
    (
        SELECT 
            o.order_id AS id,
            u.username AS nama_pelanggan,
            o.total_amount AS jumlah_dibayar,
            o.status AS status,
            o.order_date AS tanggal,
            'Online' AS metode_pembayaran
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE 
            (o.user_id LIKE :cari OR o.order_id LIKE :cari)
            AND o.status IN ('Pending')
    )
    UNION ALL
    (
        SELECT 
            oo.offline_order_id AS id,
            oo.customer_name AS nama_pelanggan,
            oo.total_amount AS jumlah_dibayar,
            oo.status AS status,
            oo.order_date AS tanggal,
            oo.payment_method AS metode_pembayaran
        FROM offline_orders oo
        WHERE 
            (oo.customer_name LIKE :cari OR oo.offline_order_id LIKE :cari)
            AND oo.status IN ('Pending')
    )
    ";

    // Jika filter 'online'
    if ($filter == 'online') {
        $sqlPesanan = "(
        SELECT 
            o.order_id AS id,
            u.username AS nama_pelanggan,
            o.total_amount AS jumlah_dibayar,
            o.status AS status,
            o.order_date AS tanggal,
            'Online' AS metode_pembayaran
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE 
            (o.user_id LIKE :cari OR o.order_id LIKE :cari)
            AND o.status IN ('Pending')
    )";
    } elseif ($filter == 'offline') {
        $sqlPesanan = "(
        SELECT 
            oo.offline_order_id AS id,
            oo.customer_name AS nama_pelanggan,
            oo.total_amount AS jumlah_dibayar,
            oo.status AS status,
            oo.order_date AS tanggal,
            oo.payment_method AS metode_pembayaran
        FROM offline_orders oo
        WHERE 
            (oo.customer_name LIKE :cari OR oo.offline_order_id LIKE :cari)
            AND oo.status IN ('Pending')
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
</head>
<body>

<div class="container">
    <h2>Kelola Riwayat Pesanan</h2>

    <form method="post" class="d-flex">
        <input type="text" name="cari_pesanan" class="form-control me-2" placeholder="Cari riwayat..." value="<?php echo htmlspecialchars($cari_pesanan); ?>">
        <select name="filter" class="form-select me-2">
            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>Semua</option>
            <option value="online" <?php echo $filter === 'online' ? 'selected' : ''; ?>>Online</option>
            <option value="offline" <?php echo $filter === 'offline' ? 'selected' : ''; ?>>Offline</option>
        </select>
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <h3>Daftar Riwayat Pesanan</h3>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>Nama Pelanggan</th>
                <th>Jumlah Dibayar</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($resultPesanan)): ?>
                <?php foreach ($resultPesanan as $rowPesanan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rowPesanan['nama_pelanggan']); ?></td>
                        <td><?php echo number_format($rowPesanan['jumlah_dibayar'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($rowPesanan['status']); ?></td>
                        <td><?php echo htmlspecialchars($rowPesanan['tanggal']); ?></td>
                        <td>
    <a href="detail_pesanan.php?id=<?php echo htmlspecialchars($rowPesanan['id']); ?>&order_id=<?php echo htmlspecialchars($rowPesanan['id']); ?>" class="btn btn-info btn-sm">Detail</a>
</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Tidak ada riwayat pesanan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
