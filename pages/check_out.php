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
$address = "Jalan Mawar, RT.002/RW.006, Mrawan, Mayang, Jember, Jawa Timur, ID 62182";

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

// Variabel untuk menyimpan pesan hasil
$order_message = '';
$rekening_number = '';  // Variabel untuk nomor rekening
$ss_file = ''; // Variabel untuk nama file SS

// Cek jika payment_method tersedia di POST
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$rekening_number = isset($_POST['rekening_number']) ? $_POST['rekening_number'] : '';

// Menangani aksi tombol "Buat Pesanan" dan menambahkan pesanan ke database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Menangani upload bukti transfer (SS)
    if (isset($_FILES['ss_file']) && $_FILES['ss_file']['error'] == 0) {
        $ss_tmp_name = $_FILES['ss_file']['tmp_name'];
        $ss_name = $_FILES['ss_file']['name'];
        $upload_dir = 'uploads/'; // Folder untuk menyimpan file SS

        // Pastikan folder uploads ada
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Membuat folder jika belum ada
        }

        $ss_file_path = $upload_dir . basename($ss_name);

        // Mengecek ekstensi file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($ss_name, PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            if (move_uploaded_file($ss_tmp_name, $ss_file_path)) {
                // Simpan nama file SS ke dalam database
                $update_query = "UPDATE orders SET ss_file = '$ss_name' WHERE order_id = $order_id";
                if (mysqli_query($conn, $update_query)) {
                    $order_message .= " Bukti transfer berhasil diunggah dan pesanan Anda sedang diproses.";
                } else {
                    $order_message .= " Error menyimpan bukti transfer ke database: " . mysqli_error($conn);
                }
            } else {
                $order_message .= " Gagal mengunggah bukti transfer.";
            }
        } else {
            $order_message .= " Ekstensi file tidak diizinkan.";
        }
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }

    .card-header {
        background-color: #343a40;
        color: #fff;
    }

    .btn-custom {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
    }

    .btn-custom:hover {
        background-color: #0056b3;
    }

    .alert-custom {
        background-color: #28a745;
        color: white;
    }

    .order-summary {
        background-color: #f1f1f1;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }

    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .section-title {
        margin-top: 15px;
        font-size: 1.125rem;
        font-weight: bold;
        color: #333;
    }

    .table td, .table th {
        padding: 0.75rem;
    }

    .form-control, .btn-custom {
        font-size: 0.875rem;
    }

    .form-check-label {
        font-size: 0.875rem;
    }

    .order-summary .d-flex {
        gap: 15px;
    }

    .order-summary p {
        margin-bottom: 8px;
    }

    .form-label {
        font-size: 0.875rem;
    }
</style>


<div class="container mt-5">
    <?php if ($order_message): ?>
        <!-- Pesan Pesanan Berhasil -->
        <div class="text-center">
            <h2 class="text-success">Pesanan Berhasil!</h2>
            <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
            <p class="lead">ID Pesanan: <?php echo $order_id; ?></p>
            <p>Pesanan Anda telah diterima dan sedang diproses. Terima kasih atas pembelian Anda!</p>
            
            <?php if ($payment_method == 'Transfer Bank' && $rekening_number): ?>
                <p><strong>Nomor Rekening untuk Pembayaran:</strong> <?php echo $rekening_number; ?></p>
            <?php endif; ?>

            <!-- Menampilkan pesan berhasil jika bukti transfer telah diunggah -->
            <?php if (strpos($order_message, 'Bukti transfer berhasil') !== false): ?>
                <div class="alert alert-success mt-4" role="alert">
                    Bukti transfer berhasil diunggah dan pesanan Anda sedang diproses.
                </div>
            <?php endif; ?>

            <!-- Form untuk unggah bukti transfer -->
            <div class="mt-4">
                <h5>Unggah Bukti Transfer</h5>
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="ss_file" class="form-label">Pilih Bukti Transfer (SS)</label>
                        <input type="file" class="form-control" id="ss_file" name="ss_file" required>
                    </div>
                    <button type="submit" class="btn btn-custom">Unggah Bukti Transfer</button>
                </form>
            </div>

            <a href="/keranjang_homemade/index.php" class="btn btn-custom mt-3">Kembali ke Beranda</a>
        </div>
    <?php else: ?>
        <!-- Informasi Pengiriman -->
        <div class="order-summary">
            <h5 class="section-title">Alamat Pengiriman</h5>
            <p><strong><?php echo $customer_name; ?></strong></p>
            <p><?php echo $phone_number; ?></p>
            <p><?php echo $address; ?></p>
        </div>

        <!-- Detail Produk -->
        <div class="order-summary">
            <h5 class="section-title">Detail Produk</h5>
            <div class="d-flex align-items-center">
                <img src="https://via.placeholder.com/100" alt="Produk" class="product-image mr-3">
                <div>
                    <h6 class="mb-1"><?php echo "Produk ID: $product_id"; ?></h6>
                    <p class="mb-1 text-muted">Rp<?php echo number_format($price, 0, ',', '.'); ?> x <?php echo $quantity; ?></p>
                    <p class="mb-0 text-danger">Rp<?php echo number_format($subtotal, 0, ',', '.'); ?></p>
                </div>
            </div>
        </div>

        <!-- Opsi Pengiriman -->
        <div class="order-summary">
            <h5 class="section-title">Opsi Pengiriman</h5>
            <p><strong>Cargo:</strong> Rp<?php echo number_format($shipping_cost, 0, ',', '.'); ?></p>
            <small class="text-muted">Garansi tiba: 3-5 Hari Kerja</small>
        </div>

        <!-- Rincian Pembayaran -->
        <div class="order-summary">
            <h5 class="section-title">Rincian Pembayaran</h5>
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

        <!-- Pilih Metode Pembayaran -->
        <div class="order-summary">
            <h5 class="section-title">Metode Pembayaran</h5>
            <form method="post">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="Transfer Bank" checked>
                    <label class="form-check-label" for="transfer">
                        Transfer Bank
                    </label>
                </div>
                <div class="mt-3">
                    <label for="rekening_number">Nomor Rekening</label>
                    <input type="text" class="form-control" id="rekening_number" name="rekening_number" required>
                </div>
                <button type="submit" class="btn btn-custom mt-3">Buat Pesanan</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
