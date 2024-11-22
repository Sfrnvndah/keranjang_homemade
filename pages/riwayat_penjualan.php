<?php
// Memanggil file koneksi
require '../database/connection.php'; // Pastikan path ini sesuai dengan lokasi file koneksi Anda

try {
    // Query untuk menggabungkan data offline dan online orders beserta detail produk
    $sql = "
        SELECT 
            'offline' AS order_type,
            o.offline_order_id AS order_id,
            o.customer_name,
            o.total_amount,
            o.order_date,
            o.payment_method,
            p.product_name,
            oi.quantity,
            p.price
        FROM offline_orders o
        JOIN offline_order_items oi ON o.offline_order_id = oi.offline_order_id
        JOIN products p ON oi.product_id = p.product_id
        
        UNION ALL
        
        SELECT 
            'online' AS order_type,
            o.order_id,
            u.username AS customer_name,
            o.total_amount,
            o.order_date,
            'Online Payment' AS payment_method,
            p.product_name,
            oi.quantity,
            p.price
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id
        
        ORDER BY order_date DESC
    ";

    // Menjalankan query
    $stmt = $pdo->prepare($sql); // Menggunakan variabel $pdo dari file koneksi
    $stmt->execute();

    // Mendapatkan hasil
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta - Keranjang Homemade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: black;
        }
        header {
            background-color: #00796b;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        main {
            padding: 20px;
        }
        section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #00796b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: auto;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .button {
            display: inline-block;
            padding: 8px 15px;
            color: white;
            background-color: #00796b;
            border: none;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #005b4f;
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
                <li class="active">
                    <a href="admin.php">
                        <img src="../assets/images/icon-dashboard.png" alt="Dashboard" width="24px" height="24px">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_produk.php">
                        <img src="../assets/images/icon-product.png" alt="Dashboard" width="24px" height="24px">
                        <span>Produk</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_pesanan.php">
                        <img src="../assets/images/icon-order.png" alt="Dashboard" width="24px" height="24px">
                        <span>Pesanan</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_pembayaran.php">
                        <img src="../assets/images/icon-payment.png" alt="Dashboard" width="24px" height="24px">
                        <span>Daftar Pembayaran</span>
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
                        <li><a href="report_sales.php">Penjualan</a></li>
                        <li><a href="report_finance.php">Keunagan</a></li>
                        <li><a href="report_payment.php">Pembayaran</a></li>
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
                        <li><a href="sales_recording.php">Penjualan Offline</a></li>
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
                        <!-- <li><a href="offline_training.php">Daftar Pelatihan Offline</a></li> -->
                        <li><a href="training_history.php">Riwayat Pendaftar</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <a href="admin/profile.php">
                        <img src="../assets/images/icon-profile.png" alt="Dashboard" width="24px" height="24px">
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
        <section>
    <h1 style="text-align: center; color: #00827f ">Riwayat Penjualan</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Tipe Order</th>
                <th>ID Order</th>
                <th>Nama Pelanggan</th>
                <th>Total Pembayaran</th>
                <th>Tanggal Order</th>
                <th>Metode Pembayaran</th>
                <th>Nama Produk</th>
                <th>Kuantitas</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($result) > 0) {
                // Output data dari setiap baris
                foreach ($result as $row) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["order_type"]) . "</td>
                            <td>" . htmlspecialchars($row["order_id"]) . "</td>
                            <td>" . htmlspecialchars($row["customer_name"]) . "</td>
                            <td>" . htmlspecialchars(number_format($row["total_amount"], 2)) . "</td>
                            <td>" . htmlspecialchars($row["order_date"]) . "</td>
                            <td>" . htmlspecialchars($row["payment_method"]) . "</td>
                            <td>" . htmlspecialchars($row["product_name"]) . "</td>
                            <td>" . htmlspecialchars($row["quantity"]) . "</td>
                            <td>" . htmlspecialchars(number_format($row["price"], 2)) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Tidak ada data.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    </section>
    </main>
</body>
</html>