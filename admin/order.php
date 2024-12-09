<?php
    if (!isset($_SESSION)) {
        session_name('admin_session');
        session_start();
    }
    require '../database/connection.php';
    if (!isset($_SESSION['admin_user_id'])) {
        header("Location: ../form/admin_login.php");
        exit;
    }

    // Menampilkan data di tabel
    try {
        // Mendapatkan tahun ini
        $currentYear = date('Y');
        // Query untuk pesanan online
        $queryOnline = "
            SELECT 
                o.order_id AS id, 
                o.order_date, 
                'Online' AS type, 
                u.username AS customer_name, 
                o.total_amount, 
                o.status
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            WHERE YEAR(o.order_date) = :currentYear
        ";
        // Query untuk pesanan offline
        $queryOffline = "
            SELECT 
                oo.offline_order_id AS id, 
                oo.order_date, 
                'Offline' AS type, 
                oo.customer_name, 
                oo.total_amount, 
                oo.status
            FROM offline_orders oo
            WHERE YEAR(oo.order_date) = :currentYear
        ";
        // Gabungkan hasil query
        $query = "($queryOnline) UNION ALL ($queryOffline) 
                  ORDER BY 
                    FIELD(status, 'Pending') DESC, 
                    order_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['currentYear' => $currentYear]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    // Filter pencarian berdasarkan status dan tanggal
    $status = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : null;
    $startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
    $endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;
    try {
        $conditions = [];
        $params = ['currentYear' => $currentYear];
        // Filter berdasarkan status jika diberikan
        if ($status) {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }
        // Filter berdasarkan tanggal awal jika diberikan
        if ($startDate) {
            $conditions[] = "order_date >= :startDate";
            $params['startDate'] = $startDate;
        }
        // Filter berdasarkan tanggal akhir jika diberikan
        if ($endDate) {
            $conditions[] = "order_date <= :endDate";
            $params['endDate'] = $endDate;
        }
        $conditionString = !empty($conditions) ? " AND " . implode(" AND ", $conditions) : "";
        $queryOnline = "
            SELECT 
                o.order_id AS id, 
                o.order_date, 
                'Online' AS type, 
                u.username AS customer_name, 
                o.total_amount, 
                o.status
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            WHERE YEAR(o.order_date) = :currentYear
            $conditionString
        ";
        $queryOffline = "
            SELECT 
                oo.offline_order_id AS id, 
                oo.order_date, 
                'Offline' AS type, 
                oo.customer_name, 
                oo.total_amount, 
                oo.status
            FROM offline_orders oo
            WHERE YEAR(oo.order_date) = :currentYear
            $conditionString
        ";
        $query = "($queryOnline) UNION ALL ($queryOffline) 
                    ORDER BY 
                        FIELD(status, 'Pending') DESC, 
                        order_date DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pesanan</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="stylesheet" href="../assets/css/training.css">
        <link rel="stylesheet" href="../assets/css/logout.css">
        <link rel="stylesheet" href="../assets/css/order.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
    </head>

    <body>
        <nav id="sidebar" style="background-color: #00827f;">
            <ul>
                <li>
                    <span class="logo" style="font-size: 16px; font-weight: bold; color: #FFF;">PAK TARA CRAFT</span>
                    <button onclick=toggleSidebar() id="toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/></svg>
                    </button>
                </li>
                <li>
                    <a href="../admin.php">
                        <img src="../assets/images/icon-dashboard.png" alt="Dashboard" width="24px" height="24px">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="product.php">
                        <img src="../assets/images/icon-product.png" alt="Dashboard" width="24px" height="24px">
                        <span>Produk</span>
                    </a>
                </li>
                <li>
                    <a href="order.php">
                        <img src="../assets/images/icon-order.png" alt="Dashboard" width="24px" height="24px">
                        <span>Pesanan</span>
                    </a>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="../assets/images/icon-report.png" alt="Dashboard" width="24px" height="24px">
                        <span>Laporan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="../report/order.php">Penjualan</a></li>
                        <li><a href="../report/income.php">Pendapatan</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="../assets/images/icon-sales.png" alt="Dashboard" width="24px" height="24px">
                        <span>Penjualan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="sales_offline.php">Penjualan Offline</a></li>
                        <li><a href="sales_history.php">Riwayat Penjualan</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="../assets/images/icon-training.png" alt="Dashboard" width="24px" height="24px">
                        <span>Pelatihan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="training_list.php">Daftar Pelatihan</a></li>
                        <li><a href="#">Daftar Peserta</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <a href="../account/profile.php">
                        <img src="../assets/images/icon-profile.png" alt="Dashboard" width="24px" height="24px">
                        <span>Profile</span>
                    </a>
                </li>
                <div class="logout-container">
                    <button class="logout-btn" onclick="window.location.href='../account/logout.php'">
                        <span>Logout</span>
                    </button>
                </div>
            </ul>
        </nav>

        <main>
            <section>
                <h2>Daftar Pesanan</h2>
                <form method="get" action="">
                    <label for="filter-status">Status :</label>
                    <select name="status" id="filter-status">
                        <option value="">Semua</option>
                        <option value="Pending" <?= isset($_GET['status']) && $_GET['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Completed" <?= isset($_GET['status']) && $_GET['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Cancelled" <?= isset($_GET['status']) && $_GET['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <label for="start-date">Tanggal Awal :</label>
                    <input type="date" id="start-date" name="start_date" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                    <label for="end-date">Tanggal Akhir :</label>
                    <input type="date" id="end-date" name="end_date" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                    <button type="submit">Cari</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal Pesanan</th>
                            <th>Jenis Pesanan</th>
                            <th>Nama Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $index => $order): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= date('d-m-Y', strtotime($order['order_date'])) ?></td>
                                    <td><?= htmlspecialchars($order['type']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                    <td style="text-align: center;">
                                        <?php if ($order['status'] === 'Pending'): ?>
                                            <span class="status-pending">Pending</span>
                                        <?php elseif ($order['status'] === 'Completed'): ?>
                                            <span class="status-completed">Completed</span>
                                        <?php elseif ($order['status'] === 'Cancelled'): ?>
                                            <span class="status-cancelled">Cancelled</span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($order['status']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="order_detail.php?type=<?= strtolower($order['type']) ?>&id=<?= $order['id'] ?>" 
                                        class="btn btn-info btn-sm">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data pesanan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </body>
</html>