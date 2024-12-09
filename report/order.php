<?php
    include('../database/connection.php');
    $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

    // Query untuk laporan penjualan
    $query = "
        SELECT 
            orders.order_date AS Tanggal,
            users.username AS Pelanggan,
            GROUP_CONCAT(products.product_name SEPARATOR ', ') AS Produk,
            'Transfer' AS Metode_Pembayaran,
            SUM(order_items.quantity) AS Jumlah_Beli,
            MIN(products.price) AS Harga,
            SUM(order_items.quantity * products.price) AS Total
        FROM orders
        JOIN order_items ON orders.order_id = order_items.order_id
        JOIN products ON order_items.product_id = products.product_id
        JOIN users ON orders.user_id = users.user_id
        WHERE orders.status = 'completed' AND YEAR(orders.order_date) = :year
        GROUP BY orders.order_date, users.username

        UNION ALL

        SELECT
            offline_orders.order_date AS Tanggal,
            offline_orders.customer_name AS Pelanggan,
            GROUP_CONCAT(products.product_name SEPARATOR ', ') AS Produk,
            offline_orders.payment_method AS Metode_Pembayaran,
            SUM(offline_order_items.quantity) AS Jumlah_Beli,
            MIN(products.price) AS Harga,
            SUM(offline_order_items.quantity * products.price) AS Total
        FROM offline_orders
        JOIN offline_order_items ON offline_orders.offline_order_id = offline_order_items.offline_order_id
        JOIN products ON offline_order_items.product_id = products.product_id
        WHERE offline_orders.status = 'completed' AND YEAR(offline_orders.order_date) = :year
        GROUP BY offline_orders.order_date, offline_orders.customer_name, offline_orders.payment_method;
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        $stmt->execute();
        $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laporan Penjualan</title>
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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
        <style>
            button[type="submit"] {
                background-color: #00827f;
                color: white;
                border: none;
                padding: 8px 16px;
                font-size: 14px;
                height: 45px;
                cursor: pointer;
                border-radius: 4px;
                transition: background-color 0.3s ease;
            }
            button[type="submit"]:hover {
                background-color: #005F5C;
            }
            button[type="button"] {
                background-color: #FE9900;
                color: white;
                border: none;
                padding: 8px 16px;
                font-size: 14px;
                height: 45px;
                cursor: pointer;
                border-radius: 4px;
                transition: background-color 0.3s ease;
            }
            button[type="button"]:hover {
                background-color: #D38003;
            }
        </style>
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
                <li class="active">
                    <a href="#">
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
                        <li><a href="#">Penjualan</a></li>
                        <li><a href="income.php">Pendapatan</a></li>
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
                        <li><a href="participants_list.php">Daftar Peserta</a></li>
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

        <main style="background-color: white">
            <section>
                <h2 style="margin-bottom: -30px">Laporan Penjualan</h2>
                <!-- Form Pemilihan Tahun -->
                <form method="GET" action="" style="display: inline-block; float: right;">
                    <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($selectedYear); ?>" min="2000" max="2099" step="1" style="width: 100px;">
                    <button type="submit" style="margin-left: 0.2rem;">Tampilkan</button>
                    <button type="button" onclick="window.location.href='generate_pdf_order.php?year=<?php echo htmlspecialchars($selectedYear); ?>'" style="margin-left: 1rem;">Download PDF</button>
                </form>
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center;">
                    <thead>
                        <tr>
                            <th style="text-align: center;">No.</th>
                            <th style="text-align: center;">Tanggal</th>
                            <th style="text-align: center;">Pelanggan</th>
                            <th style="text-align: center;">Produk</th>
                            <th style="text-align: center;">Metode Pembayaran</th>
                            <th style="text-align: center;">Jumlah Beli</th>
                            <th style="text-align: center;">Harga</th>
                            <th style="text-align: center;">Total</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 10px;">
                        <?php
                        $totalHarga = 0;
                        $totalSemua = 0;

                        if (!empty($salesData)) {
                            $no = 1;
                            foreach ($salesData as $row) {
                                // Menambahkan nilai Harga dan Total ke variabel total
                                $totalHarga += $row['Harga'];
                                $totalSemua += $row['Total'];

                                echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['Tanggal']}</td>
                                    <td>{$row['Pelanggan']}</td>
                                    <td>{$row['Produk']}</td>
                                    <td>" . ($row['Metode_Pembayaran'] ?? '-') . "</td>
                                    <td>{$row['Jumlah_Beli']}</td>
                                    <td>Rp " . number_format($row['Harga'], 0, ',', '.') . "</td>
                                    <td>Rp " . number_format($row['Total'], 0, ',', '.') . "</td>
                                </tr>";
                                $no++;
                            }

                            // Menambahkan baris untuk total keseluruhan
                            echo "<tr style='font-weight: bold;'>
                                <td colspan='6' style='text-align: right;'>Total Keseluruhan</td>
                                <td>Rp " . number_format($totalHarga, 0, ',', '.') . "</td>
                                <td>Rp " . number_format($totalSemua, 0, ',', '.') . "</td>
                            </tr>";
                        } else {
                            echo "<tr><td colspan='8'>Tidak ada data penjualan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </body>
</html>