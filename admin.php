<?php
    $notebookPath = 'graphics/python.ipynb';
    $command = 'jupyter nbconvert --to notebook --execute ' . escapeshellarg($notebookPath);
    $output = shell_exec($command);
    echo $output;
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
            LIMIT 5
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
            LIMIT 5
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
        $latestOrders = array_slice($orders, 0, 5);
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

    <body style="background-color: rgba(0, 130, 127, 0.35); color: #000;">
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
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M520-640v-160q0-17 11.5-28.5T560-840h240q17 0 28.5 11.5T840-800v160q0 17-11.5 28.5T800-600H560q-17 0-28.5-11.5T520-640ZM120-480v-320q0-17 11.5-28.5T160-840h240q17 0 28.5 11.5T440-800v320q0 17-11.5 28.5T400-440H160q-17 0-28.5-11.5T120-480Zm400 320v-320q0-17 11.5-28.5T560-520h240q17 0 28.5 11.5T840-480v320q0 17-11.5 28.5T800-120H560q-17 0-28.5-11.5T520-160Zm-400 0v-160q0-17 11.5-28.5T160-360h240q17 0 28.5 11.5T440-320v160q0 17-11.5 28.5T400-120H160q-17 0-28.5-11.5T120-160Zm80-360h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z"/></svg>
                    <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h207q16 0 30.5 6t25.5 17l57 57h320q33 0 56.5 23.5T880-640v400q0 33-23.5 56.5T800-160H160Zm0-80h640v-400H447l-80-80H160v480Zm0 0v-480 480Zm400-160v40q0 17 11.5 28.5T600-320q17 0 28.5-11.5T640-360v-40h40q17 0 28.5-11.5T720-440q0-17-11.5-28.5T680-480h-40v-40q0-17-11.5-28.5T600-560q-17 0-28.5 11.5T560-520v40h-40q-17 0-28.5 11.5T480-440q0 17 11.5 28.5T520-400h40Z"/></svg>
                    <span>Create</span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="#">Folder</a></li>
                        <li><a href="#">Document</a></li>
                        <li><a href="#">Project</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z"/></svg>
                    <span>Todo-Lists</span>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="#">Work</a></li>
                        <li><a href="#">Private</a></li>
                        <li><a href="#">Coding</a></li>
                        <li><a href="#">Gardening</a></li>
                        <li><a href="#">School</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <a href="calendar.html">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-40q0-17 11.5-28.5T280-880q17 0 28.5 11.5T320-840v40h320v-40q0-17 11.5-28.5T680-880q17 0 28.5 11.5T720-840v40h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/></svg>
                    <span>Calendar</span>
                    </a>
                </li>
                <li>
                    <a href="profile.html">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-240v-32q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v32q0 33-23.5 56.5T720-160H240q-33 0-56.5-23.5T160-240Zm80 0h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg>
                    <span>Profile</span>
                    </a>
                </li>
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
                        
                        <!-- Pesanan dan Pendapatan -->
                        <div class="row mt-4">
                            <!-- <div class="col-lg-6">
                                <div class="dashboard-card hover-effect" style="position: relative; padding: 20px; background: linear-gradient(to right, #4CAF50, #81C784); border-radius: 10px; color: white;">
                                    <h4 style="text-align: center; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chart-line" style="margin-right: 10px;"></i> Penjualan
                                    </h4>
                                    <h2 style="font-size: 4rem; text-align: center; margin: 20px 0;">
                                        <strong><?= htmlspecialchars($totalSales) ?></strong>
                                    </h2>
                                    <p style="text-align: center;">
                                        <a href="../admin/orders.php" style="text-decoration: none; color: #fff; font-weight: bold; background-color: #388E3C; padding: 10px 20px; border-radius: 5px;">
                                            Lihat Semua Pesanan
                                        </a>
                                    </p>
                                </div>
                            </div> -->
                            <!-- <div class="col-lg-6">
                                <div class="dashboard-card hover-effect animate__animated animate__fadeInUp" style="background: white; border-radius: 10px; padding: 20px; color: black; transition: all 0.3s ease; height: 228px;" 
                                    onmouseover="this.style.background='linear-gradient(to bottom, #00827f, #80E3E1)'; this.style.color='white';"
                                    onmouseout="this.style.background='white'; this.style.color='black';">
                                    <h4 style="text-align: center; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chart-line" style="margin-right: 10px;"></i> Penjualan
                                    </h4>
                                    <h2 style="font-size: 4rem; text-align: center; margin: 20px 0;">
                                        <strong><?= htmlspecialchars($totalSales) ?></strong>
                                    </h2>
                                    <p style="text-align: center;">
                                        <a href="../admin/orders.php" style="text-decoration: none; color: white; font-weight: bold; background-color: #00827f; padding: 10px 20px; border-radius: 5px; transition: all 0.3s ease;" 
                                            onmouseover="this.style.background='white'; this.style.color='#00827f';"
                                            onmouseout="this.style.background='#00827f'; this.style.color='white';">
                                            Lihat Semua Penjualan
                                        </a>
                                    </p>
                                </div>
                            </div> -->
                            <div class="col-lg-6">
                                <div class="dashboard-card hover-effect animate__animated animate__fadeInUp" 
                                    style="background: linear-gradient(to bottom, #00827f, #80E3E1); border-radius: 10px; padding: 20px; color: white; transition: all 0.3s ease; height: 228px;" 
                                    onmouseover="this.style.background='white'; this.style.color='black';"
                                    onmouseout="this.style.background='linear-gradient(to bottom, #00827f, #80E3E1)'; this.style.color='white';">
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
                                    style="background: white; border-radius: 10px; padding: 20px; color: black; transition: all 0.3s ease; height: 228px;" 
                                    onmouseover="this.style.background='linear-gradient(to bottom, #00827f, #80E3E1)'; this.style.color='white';"
                                    onmouseout="this.style.background='white'; this.style.color='black';">
                                    <h4 style="text-align: center; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-chart-line" style="margin-right: 10px;"></i> Pendapatan
                                    </h4>
                                    <p style="text-align: center; margin-top: 30px;">
                                        <h2 style="font-size: 2.7rem; text-align: center; margin: 20px 0;">
                                            <strong><?= $formattedRevenue ?></strong>
                                        </h2>
                                    </p>
                                    <p style="text-align: center; margin-top: 33px;">
                                        <a href="../admin/orders.php" 
                                        style="text-decoration: none; color: white; font-weight: bold; background-color: #00827f; padding: 10px 20px; border-radius: 5px; transition: all 0.3s ease;" 
                                        onmouseover="this.style.background='white'; this.style.color='#00827f';"
                                        onmouseout="this.style.background='#00827f'; this.style.color='white';">
                                            Lihat Laporan Pendapatan
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Produk Terlaris dan Pesanan Baru -->
                    <div class="col-lg-3">
                        <div class="dashboard-card">
                            <h5 class="bold-title">Produk Terlaris</h5>
                            <p>
                                <div class="product-legend-vertical">
                                    <?php
                                    foreach ($products as $index => $product) {
                                        $color = $productCircles['colors'][$index];
                                        $name = htmlspecialchars($product['product_name']);
                                        echo "
                                            <div class='legend-item'>
                                                <span class='legend-color' style='background-color: $color;'></span>
                                                <span class='legend-label' style='font-size: 14px; color: black;'>$name</span>
                                            </div>
                                        ";
                                    }
                                    ?>
                                </div>
                            </p>
                            <div class="product-sale">
                                <?php
                                foreach ($products as $index => $product) {
                                    $size = $productCircles['sizes'][$index];
                                    $color = $productCircles['colors'][$index];
                                    $quantity = $product['total_quantity'];
                                    echo "
                                    <div class='circle' style='background-color: $color; width: {$size}px; height: {$size}px; margin-left: -20px; z-index: " . (3 - $index) . ";'>
                                        <span>$quantity</span>
                                    </div>
                                    ";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Pesanan Baru -->
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
        <script src="assets/js/graphics.js"></script>
        <script src="assets/js/year-picker.js"></script>
        <script>
            function closePopup() {
            const popupOverlay = document.getElementById('popupOverlay');
            popupOverlay.style.display = 'none';
        }
        </script>
    </body>
</html>