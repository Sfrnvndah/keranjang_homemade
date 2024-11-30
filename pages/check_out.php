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

// Contoh data pelanggan (untuk sementara hardcoded)
$user_id = 1;  // ID pengguna yang melakukan pemesanan
$customer_name = "Pingki Sukmawati";
$phone_number = "+62 889-8907-0129";
$address = "Jalan Kh Abdur Rohim, RT.6/RW.2, Tanggir, Singgahan, Jember, Jawa Timur, ID 62361";

// Data produk (biasanya ini akan berasal dari keranjang belanja)
$product_id = 2;  // ID produk yang dibeli
$price = 50000;   // Harga produk
$quantity = 2;    // Jumlah produk yang dibeli

// Biaya tambahan
$shipping_cost = 20000; // Biaya pengiriman Cargo
$discount_shipping = 10000; // Diskon pengiriman
$service_fee = 1000; // Biaya layanan

// Menghitung total pembayaran
$subtotal = $price * $quantity;
$total_amount = $subtotal + $shipping_cost - $discount_shipping + $service_fee;
$status = 'Pending';  // Status pesanan
$order_date = date('Y-m-d H:i:s');  // Tanggal dan waktu pemesanan

// Variabel untuk menyimpan metode pembayaran
$payment_method = ''; // Default kosong, akan diisi oleh form
$order_message = ''; // Pesan untuk tampilkan setelah sukses

// Menangani aksi tombol "Buat Pesanan" dan menambahkan pesanan ke database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method']; // Mendapatkan metode pembayaran yang dipilih
    
    // 1. Menambahkan data pesanan ke tabel 'orders'
    $order_query = "INSERT INTO orders (user_id, total_amount, status, order_date, payment_method) 
                    VALUES ($user_id, $total_amount, '$status', '$order_date', '$payment_method')";

    if (mysqli_query($conn, $order_query)) {
        // Mendapatkan ID pesanan yang baru saja ditambahkan
        $order_id = mysqli_insert_id($conn);  // ID pesanan yang baru saja dimasukkan ke dalam tabel 'orders'

        // 2. Menambahkan data item pesanan ke tabel 'order_items'
        $order_item_query = "INSERT INTO order_items (order_id, product_id, quantity) 
                             VALUES ($order_id, $product_id, $quantity)";

        if (mysqli_query($conn, $order_item_query)) {
            // Menampilkan pesan setelah pesanan berhasil
            $order_message = "Pesanan berhasil dibuat! ID Pesanan: $order_id";
        } else {
            $order_message = "Error menambahkan item pesanan: " . mysqli_error($conn);
        }
    } else {
        $order_message = "Error menambahkan pesanan: " . mysqli_error($conn);
    }
}

// Menutup koneksi
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout dan Konfirmasi Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- Font Awesome untuk ikon -->
</head>
<body>

<div class="container mt-5">
    <?php if ($order_message): ?>
        <!-- Pesan Pesanan Berhasil -->
        <div class="text-center">
            <h2>Pesanan Berhasil!</h2>
            <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i> <!-- Ikon centang -->
            <p class="lead">ID Pesanan: <?php echo $order_id; ?></p>
            <p>Pesanan Anda telah diterima dan sedang diproses. Terima kasih atas pembelian Anda!</p>
            <a href="index.php" class="btn btn-primary mt-3">Kembali ke Beranda</a>
        </div>
    <?php else: ?>
        <!-- Informasi Pengiriman -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Alamat Pengiriman</h5>
                <p class="card-text">
                    <strong><?php echo $customer_name; ?></strong><br>
                    <?php echo $phone_number; ?><br>
                    <?php echo $address; ?>
                </p>
            </div>
        </div>

        <!-- Detail Produk -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detail Produk</h5>
                <div class="d-flex align-items-center">
                    <img src="https://via.placeholder.com/100" alt="Produk" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 15px;">
                    <div>
                        <h6 class="mb-1"><?php echo "Produk ID: $product_id"; ?></h6>
                        <p class="mb-1 text-muted">Rp<?php echo number_format($price, 0, ',', '.'); ?> x <?php echo $quantity; ?></p>
                        <p class="mb-0 text-danger">Rp<?php echo number_format($subtotal, 0, ',', '.'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opsi Pengiriman (Cargo) -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Opsi Pengiriman</h5>
                <p class="card-text">
                    <strong>Cargo:</strong> Rp<?php echo number_format($shipping_cost, 0, ',', '.'); ?><br>
                    <small class="text-muted">Garansi tiba: 3-5 Hari Kerja</small>
                </p>
            </div>
        </div>

        <!-- Rincian Pembayaran -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Rincian Pembayaran</h5>
                <table class="table">
                    <tr>
                        <td>Subtotal untuk Produk</td>
                        <td class="text-end">Rp<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Subtotal Pengiriman</td>
                        <td class="text-end">Rp<?php echo number_format($shipping_cost, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Biaya Layanan</td>
                        <td class="text-end">Rp<?php echo number_format($service_fee, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Diskon Pengiriman</td>
                        <td class="text-end">-Rp<?php echo number_format($discount_shipping, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th>Total Pembayaran</th>
                        <th class="text-end">Rp<?php echo number_format($total_amount, 0, ',', '.'); ?></th>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Pilih Metode Pembayaran</h5>
                <form method="post">
                    <div class="mb-3">
                        <select name="payment_method" class="form-select" required>
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet</option>
                            <option value="Cash on Delivery">Cash on Delivery</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 mt-3">Buat Pesanan</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
