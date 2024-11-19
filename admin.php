<?php
    // Grafik Penjualan
    $notebookPath = 'graphics/sales_data.ipynb';
    $command = 'jupyter nbconvert --to notebook --execute ' . escapeshellarg($notebookPath);
    $output = shell_exec($command);
    // Grafik Produk Terlaris
    $notebookPathBarChart = 'graphics/top_products.ipynb';
    $commandBarChart = 'jupyter nbconvert --to notebook --execute ' . escapeshellarg($notebookPathBarChart);
    $outputBarChart = shell_exec($commandBarChart);
    echo $outputBarChart;
    require 'database/connection.php';
    // Produk Terlaris Per-Tahun
    try {
        $currentYear = date('Y');
        $query = "
            SELECT 
                p.product_name,
                COALESCE(online.total_online_quantity, 0) AS total_online_quantity,
                COALESCE(offline.total_offline_quantity, 0) AS total_offline_quantity,
                COALESCE(online.total_online_quantity, 0) + COALESCE(offline.total_offline_quantity, 0) AS total_quantity
            FROM 
                products p
            LEFT JOIN (
                SELECT 
                    product_id, 
                    SUM(quantity) AS total_online_quantity
                FROM 
                    order_items oi
                LEFT JOIN 
                    orders o ON oi.order_id = o.order_id
                WHERE 
                    YEAR(o.order_date) = :currentYear
                GROUP BY 
                    product_id
            ) online ON p.product_id = online.product_id
            LEFT JOIN (
                SELECT 
                    product_id, 
                    SUM(quantity) AS total_offline_quantity
                FROM 
                    offline_order_items oi
                LEFT JOIN 
                    offline_orders oo ON oi.offline_order_id = oo.offline_order_id
                WHERE 
                    YEAR(oo.order_date) = :currentYear
                GROUP BY 
                    product_id
            ) offline ON p.product_id = offline.product_id
            ORDER BY 
                total_quantity DESC
            LIMIT 3;
        ";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $productCircles = [
            'colors' => ['rgba(0, 130, 127, 0.8)', 'rgba(102, 185, 183, 0.8)', 'rgba(0, 70, 68, 0.8)'],
            'sizes' => [95, 85, 75],
        ];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Total Penjualan Per-Tahun
    try {
        $currentYear = date('Y');
        $queryTotalSales = "
            SELECT 
                COALESCE(SUM(online.total_online_quantity), 0) AS total_online_sales,
                COALESCE(SUM(offline.total_offline_quantity), 0) AS total_offline_sales,
                COALESCE(SUM(online.total_online_quantity), 0) + COALESCE(SUM(offline.total_offline_quantity), 0) AS total_sales
            FROM (
                SELECT 
                    SUM(quantity) AS total_online_quantity
                FROM 
                    order_items oi
                LEFT JOIN 
                    orders o ON oi.order_id = o.order_id
                WHERE 
                    YEAR(o.order_date) = :currentYear
            ) online, (
                SELECT 
                    SUM(quantity) AS total_offline_quantity
                FROM 
                    offline_order_items oi
                LEFT JOIN 
                    offline_orders oo ON oi.offline_order_id = oo.offline_order_id
                WHERE 
                    YEAR(oo.order_date) = :currentYear
            ) offline;
        ";
        $stmtTotalSales = $pdo->prepare($queryTotalSales);
        $stmtTotalSales->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
        $stmtTotalSales->execute();
        $sales = $stmtTotalSales->fetch(PDO::FETCH_ASSOC);
        $totalSales = $sales['total_sales'] ?? 0;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    try {
        $currentYear = date('Y');
        $queryRevenue = "
            SELECT 
                COALESCE(SUM(online.total_online_amount), 0) AS total_online_revenue,
                COALESCE(SUM(offline.total_offline_amount), 0) AS total_offline_revenue,
                COALESCE(SUM(online.total_online_amount), 0) + COALESCE(SUM(offline.total_offline_amount), 0) AS total_revenue
            FROM (
                SELECT 
                    SUM(total_amount) AS total_online_amount
                FROM 
                    orders
                WHERE 
                    YEAR(order_date) = :currentYear
            ) online, (
                SELECT 
                    SUM(total_amount) AS total_offline_amount
                FROM 
                    offline_orders
                WHERE 
                    YEAR(order_date) = :currentYear
            ) offline;
        ";
        $stmtRevenue = $pdo->prepare($queryRevenue);
        $stmtRevenue->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
        $stmtRevenue->execute();
        $revenue = $stmtRevenue->fetch(PDO::FETCH_ASSOC);
        $totalRevenue = $revenue['total_revenue'] ?? 0;
        // Fungsi untuk memformat angka menjadi Rupiah
        function formatRupiah($angka)
        {
            return "Rp " . number_format($angka, 0, ',', '.');
        }
        // Format pendapatan ke dalam Rupiah
        $formattedRevenue = formatRupiah($totalRevenue);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Pesanan Terbaru
    try {
        // Query untuk online orders
        $queryOnline = "
            SELECT 
                o.order_id AS id,
                u.username AS customer_name,
                o.total_amount,
                o.status,
                o.order_date,
                'Online' AS source
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.user_id
            ORDER BY o.order_date DESC
            LIMIT 15
        ";
        // Query untuk offline orders
        $queryOffline = "
            SELECT 
                offline_order_id AS id,
                customer_name,
                total_amount,
                status,
                order_date,
                'Offline' AS source
            FROM offline_orders
            ORDER BY order_date DESC
            LIMIT 15
        ";
        // Eksekusi query online orders
        $stmtOnline = $pdo->prepare($queryOnline);
        $stmtOnline->execute();
        $onlineOrders = $stmtOnline->fetchAll(PDO::FETCH_ASSOC);
        // Eksekusi query offline orders
        $stmtOffline = $pdo->prepare($queryOffline);
        $stmtOffline->execute();
        $offlineOrders = $stmtOffline->fetchAll(PDO::FETCH_ASSOC);
        // Gabungkan data online dan offline orders
        $orders = array_merge($onlineOrders, $offlineOrders);
        // Urutkan data berdasarkan tanggal (order_date)
        usort($orders, function ($a, $b) {
            return strtotime($b['order_date']) - strtotime($a['order_date']);
        });
        // Ambil 5 pesanan terbaru setelah digabung
        $latestOrders = array_slice($orders, 0, 15);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
        <title>Pak Tara Craft - Admin Dashboard</title>
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
        <link rel="stylesheet" href="assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="assets/css/owl-carousel.css">
        <link rel="stylesheet" href="assets/css/lightbox.css">
        <link rel="stylesheet" href="assets/css/admin-dashboard.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <link rel="stylesheet" href="assets/css/sidebar.css">
        <script type="text/javascript" src="assets/js/sidebar.js" defer></script>
        <link rel="stylesheet" href="assets/css/best-seller-product.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/yearSelect/yearSelect.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/yearSelect/yearSelect.css">
        <link rel="stylesheet" href="assets/css/popup.css">
    </head>

    <body style="background-color: #fafafa; color: #000;">
        <nav id="sidebar" style="background-color: #00827f;">
            <ul>
                <li>
                    <span class="logo" style="font-size: 16px; font-weight: bold; color: #FFF;">PAK TARA CRAFT</span>
                    <button onclick=toggleSidebar() id="toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/></svg>
                    </button>
                </li>
                <li class="active">
                    <a href="admin.php">
                        <img src="assets/images/icon-dashboard.png" alt="Dashboard" width="24px" height="24px">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_produk.php">
                        <img src="assets/images/icon-product.png" alt="Dashboard" width="24px" height="24px">
                        <span>Produk</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_pesanan.php">
                        <img src="assets/images/icon-order.png" alt="Dashboard" width="24px" height="24px">
                        <span>Pesanan</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_pembayaran.php">
                        <img src="assets/images/icon-payment.png" alt="Dashboard" width="24px" height="24px">
                        <span>Daftar Pembayaran</span>
                    </a>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="assets/images/icon-report.png" alt="Dashboard" width="24px" height="24px">
                        <span>Laporan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="report_sales.php">Penjualan</a></li>
                        <li><a href="report_finance.php">Keunagan</a></li>
                        <li><a href="report_payment.php">Pembayaran</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="assets/images/icon-sales.png" alt="Dashboard" width="24px" height="24px">
                        <span>Penjualan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="sales_recording.php">Penjualan Offline</a></li>
                        <li><a href="sales_history.php">Riwayat Penjualan</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="assets/images/icon-training.png" alt="Dashboard" width="24px" height="24px">
                        <span>Pelatihan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="training_list.php">Daftar Pelatihan</a></li>
                        <li><a href="participants_list.php">Daftar Peserta</a></li>
                        <!-- <li><a href="offline_training.php">Daftar Pelatihan Offline</a></li> -->
                        <li><a href="training_history.php">Riwayat Pendaftar</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <a href="admin/profile.php">
                        <img src="assets/images/icon-profile.png" alt="Dashboard" width="24px" height="24px">
                        <span>Profile</span>
                    </a>
                </li>
                <!-- <div class="logout-container">
                    <button class="logout-btn" onclick="window.location.href='logout.php'">
                        <span>Logout</span>
                    </button>
                </div> -->
            </ul>
        </nav>       

        <main>
            <div class="container">
                <!-- Memilih Tahun -->
                <div class="year-picker-container col-lg-3 dashboard-card">
                    <input id="year-picker" type="text" class="year-picker" placeholder="Pilih Tahun">
                    <button id="submit-year" class="btn btn-primary">Submit</button>
                </div>
                <div class="row">
                    <!-- Grafik Penjualan -->
                    <div class="col-lg-9">
                        <div class="dashboard-card">
                            <h5>Grafik Penjualan</h5>
                            <select id="sales-filter">
                                <option value="all">Semua Penjualan</option>
                                <option value="online">Penjualan Online</option>
                                <option value="offline">Penjualan Offline</option>
                            </select>
                            <canvas id="salesChart"></canvas>
                        </div>
                        
                        <!-- Grafik produk terlaris -->
                        <div class="dashboard-card">
                            <h5>Produk Terlaris</h5>
                            <canvas id="topProductsChart"></canvas>
                        </div>


                        <!-- Pesanan dan Pendapatan -->
                        <div class="row mt-4">
                            <div class="col-lg-6">
                                <div class="dashboard-card hover-effect animate__animated animate__fadeInUp" 
                                    style="background: linear-gradient(to bottom, #00827f, #4DADAB); border-radius: 10px; padding: 20px; color: white; transition: all 0.3s ease; height: 220px;" 
                                    onmouseover="this.style.background='white'; this.style.color='black';"
                                    onmouseout="this.style.background='linear-gradient(to bottom, #00827f, #4DADAB)'; this.style.color='white';">
                                    <h4 style="text-align: center; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chart-line" style="margin-right: 10px;"></i> Penjualan
                                    </h4>
                                    <h2 style="font-size: 4rem; text-align: center; margin: 20px 0;">
                                        <strong><?= htmlspecialchars($totalSales) ?></strong>
                                    </h2>
                                    <p style="text-align: center;">
                                        <a href="../admin/orders.php" style="text-decoration: none; color: white; font-weight: bold; background-color: #00827f; padding: 10px 20px; border-radius: 5px; transition: all 0.3s ease;" 
                                        onmouseover="this.style.background='#00827f'; this.style.color='white';"
                                        onmouseout="this.style.background='white'; this.style.color='#00827f';">
                                            Lihat Semua Pesanan
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="dashboard-card hover-effect animate__animated animate__fadeInUp" 
                                style="background: linear-gradient(to bottom, #00827f, #4DADAB); border-radius: 10px; padding: 20px; color: white; transition: all 0.3s ease; height: 220px;" 
                                    onmouseover="this.style.background='white'; this.style.color='black';"
                                    onmouseout="this.style.background='linear-gradient(to bottom, #00827f, #4DADAB)'; this.style.color='white';">
                                    <h4 style="text-align: center; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chart-line" style="margin-right: 10px;"></i> Pendapatan
                                    </h4>
                                    <p style="text-align: center; margin-top: 30px;">
                                        <h2 style="font-size: 2.7rem; text-align: center; margin: 20px 0;">
                                            <strong><?= $formattedRevenue ?></strong>
                                        </h2>
                                    </p>
                                    <p style="text-align: center; margin-top: 33px;">
                                        <a href="../admin/report_finance.php" 
                                        style="text-decoration: none; color: white; font-weight: bold; background-color: #00827f; padding: 10px 20px; border-radius: 5px; transition: all 0.3s ease;" 
                                        onmouseover="this.style.background='#00827f'; this.style.color='white';"
                                        onmouseout="this.style.background='white'; this.style.color='#00827f';">
                                            Lihat Laporan Keuangan
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pesanan Baru -->
                    <div class="col-lg-3">
                        <div class="dashboard-card">
                            <div class="card-header" style="background-color: rgba(0, 130, 127, 10);">
                                <h5 style="font-weight: bold; color: white;">Pesanan Terbaru</h5>
                                <p>
                                    <a href="../admin/orders.php" class="view-all">Lihat Semua Pesanan</a>
                                </p>
                            </div>
                            <ul class="order-list">
                                <?php foreach ($latestOrders as $order): ?>
                                    <li>
                                        <strong><?php echo $order['id']; ?></strong>
                                        <?php if ($order['customer_name']): ?>
                                            - <?php echo $order['customer_name']; ?>
                                        <?php endif; ?>
                                        <span class="order-status <?php echo $order['source']; ?> <?php echo $order['status']; ?>">
                                            <?php echo $order['source']; ?> - <?php echo $order['status']; ?>
                                        </span>
                                        <span><?php echo $order['order_date']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popup-overlay" id="popupOverlay" style="display: none;">
                <div class="popup-content">
                    <p id="popupMessage"></p>
                    <button onclick="closePopup()">Close</button>
                </div>
            </div>
        </main>
        <script src="assets/js/line-chart.js"></script>
        <script src="assets/js/bar-chart.js"></script>
        <script src="assets/js/year-picker.js"></script>
        <script>
            function closePopup() {
            const popupOverlay = document.getElementById('popupOverlay');
            popupOverlay.style.display = 'none';
        }
        </script>
    </body>
</html>