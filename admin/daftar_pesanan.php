<?php
// Koneksi ke database
$server = "localhost";
$username = "root";
$password = "";
$database = "keranjang_homemade";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Mulai session sebelum ada output HTML
session_start();

// Cek apakah ada permintaan untuk logout
if (isset($_GET['logout'])) {
    // Hapus session
    session_unset();
    session_destroy();

    // Redirect ke halaman admin
    header("Location: http://localhost/keranjang_homemade/admin.php");
    exit();
}

// Inisialisasi variabel
$order = null;
$offline_order = null;
$query_orders = ""; // Menetapkan query orders sebagai string kosong

// Mengecek apakah ada permintaan untuk menampilkan detail pesanan
if (isset($_GET['page'])) {
    $page = $_GET['page'];

    // Menampilkan detail pesanan online
    if ($page == 'order_details' && isset($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']); // Validasi ID untuk mencegah SQL Injection
    
        // Query untuk mendapatkan detail pesanan
        $query_order_details = "
            SELECT 
                o.order_id, 
                u.username, 
                o.total_amount, 
                o.status, 
                o.order_date
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            WHERE o.order_id = ?";
        
        // Persiapan query
        $stmt = mysqli_prepare($conn, $query_order_details);
        if (!$stmt) {
            die("Kesalahan pada prepare statement: " . mysqli_error($conn));
        }
    
        // Bind parameter dan eksekusi
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $result_order_details = mysqli_stmt_get_result($stmt);
        $order = mysqli_fetch_assoc($result_order_details);
    
        if (!$order) {
            die("Pesanan tidak ditemukan.");
        }
    
        // Query untuk mendapatkan item pesanan
        $query_order_items = "
            SELECT 
                oi.order_item_id, 
                p.product_name, 
                oi.quantity, 
                p.price
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?";
        
        // Persiapan query untuk item pesanan
        $stmt_items = mysqli_prepare($conn, $query_order_items);
        if (!$stmt_items) {
            die("Kesalahan pada prepare statement: " . mysqli_error($conn));
        }
    
        // Bind parameter dan eksekusi
        mysqli_stmt_bind_param($stmt_items, "i", $order_id);
        mysqli_stmt_execute($stmt_items);
        $result_order_items = mysqli_stmt_get_result($stmt_items);
    }

    // Menampilkan detail pesanan offline
    if ($page == 'offline_order_details' && isset($_GET['offline_order_id'])) {
        $offline_order_id = intval($_GET['offline_order_id']); // Validasi ID untuk mencegah SQL Injection
        $query_offline_order_details = "
            SELECT 
                o.offline_order_id, 
                o.customer_name, 
                o.total_amount, 
                o.status, 
                o.order_date, 
                o.payment_method
            FROM offline_orders o
            WHERE o.offline_order_id = ?";
        
        $stmt = mysqli_prepare($conn, $query_offline_order_details);
        mysqli_stmt_bind_param($stmt, "i", $offline_order_id);
        mysqli_stmt_execute($stmt);
        $result_offline_order_details = mysqli_stmt_get_result($stmt);
        $offline_order = mysqli_fetch_assoc($result_offline_order_details);

        if (!$offline_order) {
            die("Pesanan offline tidak ditemukan.");
        }

        // Menampilkan item pesanan offline
        $query_offline_order_items = "
            SELECT oi.offline_order_items_id, p.product_name, oi.quantity, p.price
            FROM offline_order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.offline_order_id = ?";
        
        $stmt_items = mysqli_prepare($conn, $query_offline_order_items);
        mysqli_stmt_bind_param($stmt_items, "i", $offline_order_id);
        mysqli_stmt_execute($stmt_items);
        $result_offline_order_items = mysqli_stmt_get_result($stmt_items);
    }
} else {
    // Menampilkan daftar pesanan online, dengan hanya satu pesanan terbaru per pelanggan
    $query_orders = "
        SELECT o.order_id, u.username, o.total_amount, o.status, o.order_date
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id IN (
            SELECT MAX(order_id)
            FROM orders
            GROUP BY user_id
        )
        ORDER BY o.order_date DESC
    "; // Pastikan query ditutup dengan benar

    $result_orders = mysqli_query($conn, $query_orders);

    if (!$result_orders) {
        die("Query gagal: " . mysqli_error($conn));
    }

    $query_offline_orders = "
        SELECT o.offline_order_id, o.customer_name, o.total_amount, o.status, o.order_date, o.payment_method
        FROM offline_orders o";
    $result_offline_orders = mysqli_query($conn, $query_offline_orders);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <?php if (isset($order)): ?>
        <h1 class="text-center mb-4">Detail Pesanan Online</h1>
        <div class="card">
            <div class="card-header">
                <h5>Pesanan Online ID: <?= $order['order_id']; ?></h5>
            </div>
            <div class="card-body">
                <p><strong>Nama Pengguna:</strong> <?= $order['username']; ?></p>
                <p><strong>Total Pembayaran:</strong> Rp<?= number_format($order['total_amount'], 0, ',', '.'); ?></p>
                <p><strong>Status:</strong> <?= $order['status']; ?></p>
                <p><strong>Tanggal Pemesanan:</strong> <?= $order['order_date']; ?></p>
                
                <h4>Daftar Item Pesanan</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Kuantitas</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($item = mysqli_fetch_assoc($result_order_items)) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$item['product_name']}</td>
                                    <td>Rp" . number_format($item['price'], 0, ',', '.') . "</td>
                                    <td>{$item['quantity']}</td>
                                    <td>Rp" . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "</td>
                                  </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
                <a href="daftar_pesanan.php" class="btn btn-primary">Kembali ke Daftar Pesanan</a>
            </div>
        </div>
    <?php elseif (isset($offline_order)): ?>
        <h1 class="text-center mb-4">Detail Pesanan Offline</h1>
        <div class="card">
            <div class="card-header">
                <h5>Pesanan Offline ID: <?= $offline_order['offline_order_id']; ?></h5>
            </div>
            <div class="card-body">
                <p><strong>Nama Pelanggan:</strong> <?= $offline_order['customer_name']; ?></p>
                <p><strong>Total Pembayaran:</strong> Rp<?= number_format($offline_order['total_amount'], 0, ',', '.'); ?></p>
                <p><strong>Status:</strong> <?= $offline_order['status']; ?></p>
                <p><strong>Tanggal Pemesanan:</strong> <?= $offline_order['order_date']; ?></p>
                <p><strong>Metode Pembayaran:</strong> <?= $offline_order['payment_method']; ?></p>
                
                <h4>Daftar Item Pesanan</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Kuantitas</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($item = mysqli_fetch_assoc($result_offline_order_items)) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$item['product_name']}</td>
                                    <td>Rp" . number_format($item['price'], 0, ',', '.') . "</td>
                                    <td>{$item['quantity']}</td>
                                    <td>Rp" . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "</td>
                                  </tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
                <a href="daftar_pesanan.php" class="btn btn-primary">Kembali ke Daftar Pesanan</a>
            </div>
        </div>
    <?php else: ?>
        <h1 class="text-center mb-4">Daftar Pesanan</h1>
        <h2>Pesanan Online Terbaru</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pengguna</th>
                    <th>Total Pembayaran</th>
                    <th>Status</th>
                    <th>Tanggal Pemesanan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($order = mysqli_fetch_assoc($result_orders)) {
                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$order['username']}</td>
                            <td>Rp" . number_format($order['total_amount'], 0, ',', '.') . "</td>
                            <td>{$order['status']}</td>
                            <td>{$order['order_date']}</td>
                            <td><a href=\"?page=order_details&order_id={$order['order_id']}\" class=\"btn btn-info\">Detail</a></td>
                          </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>

        <h2>Pesanan Offline</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Total Pembayaran</th>
                    <th>Status</th>
                    <th>Tanggal Pemesanan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($offline_order = mysqli_fetch_assoc($result_offline_orders)) {
                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$offline_order['customer_name']}</td>
                            <td>Rp" . number_format($offline_order['total_amount'], 0, ',', '.') . "</td>
                            <td>{$offline_order['status']}</td>
                            <td>{$offline_order['order_date']}</td>
                            <td><a href=\"?page=offline_order_details&offline_order_id={$offline_order['offline_order_id']}\" class=\"btn btn-info\">Detail</a></td>
                          </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>