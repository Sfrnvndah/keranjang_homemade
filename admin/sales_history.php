<?php
    require '../database/connection.php';

    try {
        $current_year = date('Y');
        $formatted_date = isset($_POST['formatted_tanggal']) ? $_POST['formatted_tanggal'] : null;
        $query = "
            SELECT 
                'online' AS order_type,
                o.order_date AS order_date,
                u.name AS customer_name,
                p.product_name AS product_name,
                oi.quantity AS quantity,
                (oi.quantity * p.price) AS subtotal,
                o.total_amount AS total,
                'Online Payment' AS payment_method
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN products p ON oi.product_id = p.product_id
            JOIN users u ON o.user_id = u.user_id
            WHERE YEAR(o.order_date) = :current_year";
        if ($formatted_date) {
            $query .= " AND o.order_date = :formatted_date";
        }
        $query .= " UNION ALL
            SELECT 
                'offline' AS order_type,
                oo.order_date AS order_date,
                oo.customer_name AS customer_name,
                p.product_name AS product_name,
                ooi.quantity AS quantity,
                (ooi.quantity * p.price) AS subtotal,
                oo.total_amount AS total,
                oo.payment_method AS payment_method
            FROM offline_orders oo
            JOIN offline_order_items ooi ON oo.offline_order_id = ooi.offline_order_id
            JOIN products p ON ooi.product_id = p.product_id
            WHERE YEAR(oo.order_date) = :current_year";
        if ($formatted_date) {
            $query .= " AND oo.order_date = :formatted_date";
        }
        $query .= " ORDER BY order_date DESC;";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':current_year', $current_year, PDO::PARAM_INT);
        if ($formatted_date) {
            $stmt->bindParam(':formatted_date', $formatted_date, PDO::PARAM_STR);
        }
        $stmt->execute();
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>


<!DOCTYPE html>
    <html lang="id">
    <head>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Riwayat Penjualan</title>
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
        <link rel="stylesheet" href="../assets/css/participants-list.css">
        <link rel="stylesheet" href="../assets/css/logout.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                        <li><a href="../report/report_sales.php">Penjualan</a></li>
                        <li><a href="../report/report_finance.php">Keuangan</a></li>
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
                        <li><a href="#">Riwayat Penjualan</a></li>
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

        <main>
            <section>
                <h1 style="text-align: center; color: #00827f ">Riwayat Penjualan</h1>
                <form method="POST" action="sales_history.php">
                    <div class="col-lg-3" style="display: flex; align-items: center; gap: 10px; margin-bottom: -20px">
                        <div>
                            <label for="tanggal">Tanggal :</label>
                            <input type="text" id="tanggal" name="formatted_tanggal" required placeholder="dd/mm/yyyy">
                        </div>
                        <button type="submit" class="btn btn-submit-date">Submit</button>
                    </div>
                </form>
                <table border="1">
                    <thead>
                        <tr>
                            <th style="text-align: center;">No.</th>
                            <th style="text-align: center;">Tanggal</th>
                            <th style="text-align: center;">Nama Pelanggan</th>
                            <th style="text-align: center;">Produk</th>
                            <th style="text-align: center;">Jumlah</th>
                            <th style="text-align: center;">Subtotal</th>
                            <th style="text-align: center;">Total</th>
                            <th style="text-align: center;">Metode Pembayaran</th>
                            <th style="text-align: center;">Jenis</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 12px;">
                        <?php 
                        $current_date = null;
                        $current_customer = null;
                        $no = 1;
                        foreach ($sales as $key => $sale): 
                            $merge_cells = (
                                $sale['order_date'] !== $current_date || 
                                $sale['customer_name'] !== $current_customer
                            );
                            if ($merge_cells) {
                                $current_date = $sale['order_date'];
                                $current_customer = $sale['customer_name'];
                            }
                        ?>
                        <tr>
                            <?php if ($merge_cells): ?>
                                <td rowspan="<?= count(array_filter($sales, fn($s) => $s['order_date'] === $sale['order_date'] && $s['customer_name'] === $sale['customer_name'])) ?>">
                                    <?= $no++ ?>
                                </td>
                                <td rowspan="<?= count(array_filter($sales, fn($s) => $s['order_date'] === $sale['order_date'] && $s['customer_name'] === $sale['customer_name'])) ?>">
                                    <?= $sale['order_date'] ?>
                                </td>
                                <td rowspan="<?= count(array_filter($sales, fn($s) => $s['order_date'] === $sale['order_date'] && $s['customer_name'] === $sale['customer_name'])) ?>">
                                    <?= $sale['customer_name'] ?>
                                </td>
                            <?php endif; ?>
                            <td><?= $sale['product_name'] ?></td>
                            <td><?= $sale['quantity'] ?></td>
                            <td><?= number_format($sale['subtotal'], 2) ?></td>
                            <?php if ($merge_cells): ?>
                                <td rowspan="<?= count(array_filter($sales, fn($s) => $s['order_date'] === $sale['order_date'] && $s['customer_name'] === $sale['customer_name'])) ?>">
                                    <?= number_format($sale['total'], 2) ?>
                                </td>
                                <td rowspan="<?= count(array_filter($sales, fn($s) => $s['order_date'] === $sale['order_date'] && $s['customer_name'] === $sale['customer_name'])) ?>">
                                    <?= $sale['payment_method'] ?>
                                </td>
                                <td rowspan="<?= count(array_filter($sales, fn($s) => $s['order_date'] === $sale['order_date'] && $s['customer_name'] === $sale['customer_name'])) ?>">
                                    <?= $sale['order_type'] ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            document.querySelector('.btn-submit-date').addEventListener('click', function(event) {
                event.preventDefault();
                var tanggalInput = document.getElementById('tanggal').value;
                var dateParts = tanggalInput.split('/');
                if (dateParts.length === 3) {
                    var formattedDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0];
                    var hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'formatted_tanggal';
                    hiddenInput.value = formattedDate;
                    var form = document.querySelector('form');
                    form.appendChild(hiddenInput);
                    form.submit();
                } else {
                    alert('Format tanggal salah. Harap masukkan tanggal dengan format dd/mm/yyyy');
                }
            });
        </script>
<script>
    flatpickr("#tanggal", {
        dateFormat: "d/m/Y", // Use dd/mm/yyyy format
    });
</script>

    </body>
</html>