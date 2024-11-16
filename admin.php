<?php
    $notebookPath = 'graphics/python.ipynb';
    $command = 'jupyter nbconvert --to notebook --execute ' . escapeshellarg($notebookPath);
    $output = shell_exec($command);
    echo $output;
    require 'database/connection.php';
    try {
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
                    order_items
                GROUP BY 
                    product_id
            ) online ON p.product_id = online.product_id
            LEFT JOIN (
                SELECT 
                    product_id, 
                    SUM(quantity) AS total_offline_quantity
                FROM 
                    offline_order_items
                GROUP BY 
                    product_id
            ) offline ON p.product_id = offline.product_id
            ORDER BY 
                total_quantity DESC
            LIMIT 3;
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $productCircles = [
            'colors' => ['rgba(0, 130, 127, 0.8)', 'rgba(102, 185, 183, 0.8)', 'rgba(0, 70, 68, 0.8)'],
            'sizes' => [80, 70, 60],
        ];
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

    </head>

    <body style="background-color: rgba(0, 130, 127, 0.35); color: #000;">
        <nav id="sidebar" style="background-color: #00827f;">
            <ul>
            <li>
                <span class="logo">coding2go</span>
                <button onclick=toggleSidebar() id="toggle-btn">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/></svg>
                </button>
            </li>
            <li class="active">
                <a href="admin.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M240-200h120v-200q0-17 11.5-28.5T400-440h160q17 0 28.5 11.5T600-400v200h120v-360L480-740 240-560v360Zm-80 0v-360q0-19 8.5-36t23.5-28l240-180q21-16 48-16t48 16l240 180q15 11 23.5 28t8.5 36v360q0 33-23.5 56.5T720-120H560q-17 0-28.5-11.5T520-160v-200h-80v200q0 17-11.5 28.5T400-120H240q-33 0-56.5-23.5T160-200Zm320-270Z"/></svg>
                <span>Home</span>
                </a>
            </li>
            <li>
                <a href="dashboard.html">
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
                <div class="row">
                    <!-- Grafik Penjualan -->
                    <div class="col-lg-12">
                        <div class="dashboard-card">
                            <h5>Grafik Penjualan</h5>
                            <select id="year-filter"></select>
                            <select id="sales-filter">
                                <option value="all">Semua Penjualan</option>
                                <option value="online">Penjualan Online</option>
                                <option value="offline">Penjualan Offline</option>
                            </select>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <!-- Kolom (History, Pembayaran, Pesanan) -->
                    <div class="col-lg-3">
                        <div class="dashboard-card">
                            <h6>Pesanan Baru</h6>
                            <p><a href="../admin/orders.php">Lihat Semua Pesanan</a></p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="dashboard-card">
                            <h4>Pesanan</h4>
                            <p><a href="../admin/orders.php">Lihat Semua Pesanan</a></p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="dashboard-card">
                            <h4>Pesanan</h4>
                            <p><a href="../admin/orders.php">Lihat Semua Pesanan</a></p>
                        </div>
                    </div>

                    <!-- Kolom Lingkaran -->
                    <div class="col-lg-3">
                        <div class="dashboard-card">
                            <h6>Produk Terlaris</h6>
                            <!-- Legend Produk Terlaris -->
                            <p>
                                <div class="product-legend-vertical">
                                    <?php
                                    // Generate legend secara dinamis
                                    foreach ($products as $index => $product) {
                                        $color = $productCircles['colors'][$index];
                                        $name = htmlspecialchars($product['product_name']);
                                        echo "
                                        <div class='legend-item'>
                                            <span class='legend-color' style='background-color: $color;'></span>
                                            <span class='legend-label'>$name</span>
                                        </div>
                                        ";
                                    }
                                    ?>
                                </div>
                            </p>
                            <!-- Lingkaran Produk -->
                            <div class="product-sale">
                                <?php
                                // Generate lingkaran secara dinamis
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
                    </div>

                    <!-- Statistik Pesanan dan Pendapatan -->
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="dashboard-card">
                                    <h4>Statistik Pesanan</h4>
                                    <p>Total Pesanan: <span id="order-count">123</span></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="dashboard-card">
                                    <h4>Statistik Pendapatan</h4>
                                    <p>Total Pendapatan: Rp<span id="revenue">5,000,000</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pesanan Baru dan Notifikasi Update -->
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="dashboard-card">
                                    <h4>Pesanan Baru</h4>
                                    <ul class="order-list">
                                        <li>Order #1234 - Rp500,000</li>
                                        <li>Order #1235 - Rp1,200,000</li>
                                        <li>Order #1236 - Rp350,000</li>
                                        <li><a href="../admin/orders.php">Lihat semua pesanan</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="dashboard-card">
                                    <h4>Notifikasi Update</h4>
                                    <ul class="notification-list">
                                        <li>Order #1231 telah dikirim</li>
                                        <li>Pengembalian untuk Order #1229 diproses</li>
                                        <li>Order #1232 dibatalkan</li>
                                        <li><a href="../admin/notifications.php">Lihat semua notifikasi</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </main>

        <script src="assets/js/graphics.js"></script>
    </body>
</html>
