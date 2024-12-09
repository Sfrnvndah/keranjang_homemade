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
    if (!isset($_GET['type'], $_GET['id'])) {
        header("Location: order.php");
        exit;
    }

    $type = $_GET['type'];
    $id = $_GET['id'];

    try {
        if ($type === 'online') {
            // Query untuk detail pesanan online
            $queryOrder = "
                SELECT 
                    o.order_id AS id, 
                    o.order_date, 
                    u.username AS customer_name, 
                    o.total_amount, 
                    o.status,
                    o.payment_image
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                WHERE o.order_id = :id
            ";
            $queryItems = "
                SELECT 
                    p.product_name, 
                    oi.quantity, 
                    p.price, 
                    (oi.quantity * p.price) AS subtotal
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = :id
            ";
        } elseif ($type === 'offline') {
            $queryOrder = "
                SELECT 
                    oo.offline_order_id AS id, 
                    oo.order_date, 
                    oo.customer_name, 
                    oo.total_amount, 
                    oo.status, 
                    oo.payment_method
                FROM offline_orders oo
                WHERE oo.offline_order_id = :id
            ";
            $queryItems = "
                SELECT 
                    p.product_name, 
                    ooi.quantity, 
                    p.price, 
                    (ooi.quantity * p.price) AS subtotal
                FROM offline_order_items ooi
                JOIN products p ON ooi.product_id = p.product_id
                WHERE ooi.offline_order_id = :id
            ";
        } else {
            throw new Exception("Jenis pesanan tidak valid.");
        }
        // Eksekusi query utama
        $stmtOrder = $pdo->prepare($queryOrder);
        $stmtOrder->execute(['id' => $id]);
        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);
        if (!$order) {
            throw new Exception("Pesanan tidak ditemukan.");
        }
        // Eksekusi query detail item
        $stmtItems = $pdo->prepare($queryItems);
        $stmtItems->execute(['id' => $id]);
        $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        // Update status jika form disubmit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
            $newStatus = $_POST['status'];

            if ($type === 'online') {
                $updateQuery = "UPDATE orders SET status = :status WHERE order_id = :id";
            } elseif ($type === 'offline') {
                $updateQuery = "UPDATE offline_orders SET status = :status WHERE offline_order_id = :id";
            } else {
                throw new Exception("Jenis pesanan tidak valid.");
            }
            // Eksekusi query update status
            $stmtUpdate = $pdo->prepare($updateQuery);
            $stmtUpdate->execute(['status' => $newStatus, 'id' => $id]);
            // Arahkan kembali ke halaman order setelah update
            header("Location: order.php");
            exit;
        }
    } catch (Exception $e) {
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
        <title>Detail Pesanan</title>
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
        <link rel="stylesheet" href="../assets/css/order-detail.css">
        <link rel="stylesheet" href="../assets/css/logout.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
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

        <main style="background: #f8f8f8;">
            <div class="container">
                <div class="card">
                    <h3>Detail Pesanan</h3>
                    <!-- Tanggal -->
                    <div class="detail-row">
                        <span class="detail-label">Tanggal Pesanan</span>
                        <span class="detail-value"><?= date('d-m-Y', strtotime($order['order_date'])) ?></span>
                    </div>
                    <!-- Nama pelanggan -->
                    <div class="detail-row">
                        <span class="detail-label">Nama Pelanggan</span>
                        <span class="detail-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                    </div>
                    <!-- Total -->
                    <div class="detail-row">
                        <span class="detail-label">Total</span>
                        <span class="detail-value">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                    </div>
                    <!-- Gambar Bukti Pembayaran -->
                    <?php if (!empty($order['payment_image'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Bukti Pembayaran</span>
                            <div class="detail-value">
                                <a href="../assets/images/<?= htmlspecialchars($order['payment_image']) ?>" data-lightbox="payment-image-group">
                                    <img src="../assets/images/<?= htmlspecialchars($order['payment_image']) ?>" alt="Bukti Pembayaran" style="max-width: 100px; cursor: pointer;">
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- Status Pesanan -->
                    <form method="POST">
                        <div class="detail-row">
                            <span class="detail-label">Status</span>
                            <div class="detail-value">
                                <select id="status-dropdown" name="status" class="status-dropdown">
                                    <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <!-- Metode Pembayaran -->
                        <?php if ($type === 'offline'): ?>
                            <div class="detail-row">
                                <span class="detail-label">Metode Pembayaran</span>
                                <span class="detail-value"><?= htmlspecialchars($order['payment_method']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div style="text-align: left; margin-top: 10px;">
                            <button type="submit" class="btn-simpan-perubahan">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

                <!-- Tabel Produk -->
                <div class="card">
                    <h4>Detail Produk</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $index => $item): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- <a href="order.php" class="btn btn-primary">Kembali</a> -->
            </div>
        </main>

        <!-- Popup Gambar -->
        <div class="popup-overlay" id="popup-overlay">
            <span class="popup-close" id="popup-close">&times;</span>
            <div class="popup-content" id="popup-content">
                <img src="" alt="Bukti Pembayaran" id="popup-image">
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
        <script>
            document.querySelectorAll('.payment-image-popup img').forEach(img => {
                img.addEventListener('click', function () {
                    const popupOverlay = document.getElementById('popup-overlay');
                    const popupImage = document.getElementById('popup-image');
                    popupImage.src = this.src;
                    popupOverlay.style.display = 'flex';
                });
            });
            document.getElementById('popup-overlay').addEventListener('click', function (e) {
                if (e.target === this || e.target.id === 'popup-close') {
                    this.style.display = 'none';
                }
            });
        </script>
    </body>
</html>
