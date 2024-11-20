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

// Ambil produk dari database untuk ditampilkan di dropdown
$products_result = mysqli_query($conn, "SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-Out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .checkout-container {
            margin-top: 50px;
        }
        .checkout-form {
            background-color: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .checkout-form h3 {
            margin-bottom: 20px;
            color: #800000;
        }
        .form-section {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
        }
        .btn-primary {
            background-color: #800000;
            border-color: #800000;
            width: 100%;
            padding: 12px;
        }
        .btn-primary:hover {
            background-color: #5c0000;
            border-color: #5c0000;
        }
        .total-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #800000;
        }
        .form-section h4 {
            color: #800000;
        }
        .form-control, .form-select {
            border: 1px solid #800000;
        }
        .form-control:focus, .form-select:focus {
            border-color: #800000;
            box-shadow: 0 0 0 0.25rem rgba(128, 0, 0, 0.25);
        }
    </style>
</head>
<body>

<div class="container checkout-container">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="checkout-form">
                <h3>Formulir Check-Out</h3>
                <form action="" method="POST">
                    <!-- Rincian Produk -->
                    <div class="form-section">
                        <h4>Rincian Produk</h4>
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Pilih Produk</label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                <?php while ($product = mysqli_fetch_assoc($products_result)) { ?>
                                    <option value="<?php echo $product['id']; ?>">
                                        <?php echo $product['name'] . " - $" . $product['price']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Jumlah</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" required>
                        </div>
                    </div>

                    <!-- Informasi Pelanggan -->
                    <div class="form-section">
                        <h4>Informasi Pelanggan</h4>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Pengiriman</label>
                            <input type="text" id="address" name="address" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Nomor Telepon</label>
                            <input type="text" id="phone_number" name="phone_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="form-section">
                        <h4>Metode Pembayaran</h4>
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="bank_account" class="form-label">Nomor Rekening</label>
                            <input type="text" name="bank_account" id="bank_account" class="form-control" required>
                        </div>
                        <!-- Jenis Kartu -->
                        <div class="mb-3">
                            <label for="card_type" class="form-label">Jenis Kartu</label>
                            <select name="card_type" id="card_type" class="form-select">
                                <option value="kredit">Kartu Kredit</option>
                                <option value="debit">Kartu Debit</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rincian Pengiriman -->
                    <div class="form-section">
                        <h4>Rincian Pengiriman</h4>
                        <div class="mb-3">
                            <label for="shipping_method" class="form-label">Metode Pengiriman</label>
                            <select name="shipping_method" id="shipping_method" class="form-select" required>
                                <option value="kurir">Kurir</option>
                                <option value="pos">Pos</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="shipping_cost" class="form-label">Biaya Pengiriman</label>
                            <input type="number" name="shipping_cost" id="shipping_cost" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="delivery_time" class="form-label">Estimasi Waktu Pengiriman</label>
                            <input type="date" name="delivery_time" id="delivery_time" class="form-control" required>
                        </div>
                    </div>

                    <!-- Total Pembayaran -->
                    <div class="form-section">
                        <h4>Total Pembayaran</h4>
                        <p>Total: <span class="total-price" id="total_amount">$0.00</span></p>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Proses Pembayaran</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($_POST['submit'])) {
    // Ambil data dari form
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $payment_method = $_POST['payment_method'];
    $bank_account = $_POST['bank_account'];
    $card_type = $_POST['card_type'];  // Jenis kartu
    $shipping_method = $_POST['shipping_method'];
    $shipping_cost = $_POST['shipping_cost'];
    $delivery_time = $_POST['delivery_time'];

    // Simpan informasi pelanggan ke database
    $query = "INSERT INTO customers (full_name, address, phone_number, email) 
              VALUES ('$full_name', '$address', '$phone_number', '$email')";
    mysqli_query($conn, $query);
    $customer_id = mysqli_insert_id($conn);

    // Ambil harga produk dari database
    $product_query = "SELECT * FROM products WHERE id = '$product_id'";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);
    $total_price = $product['price'] * $quantity;

    // Simpan pesanan ke database
    $order_query = "INSERT INTO orders (customer_id, total_price, shipping_address, shipping_method, shipping_cost, delivery_time) 
                    VALUES ('$customer_id', '$total_price', '$address', '$shipping_method', '$shipping_cost', '$delivery_time')";
    mysqli_query($conn, $order_query);
    $order_id = mysqli_insert_id($conn);

    // Simpan metode pembayaran dan jenis kartu
    $payment_query = "INSERT INTO payments (order_id, payment_method, bank_account, card_type) 
                      VALUES ('$order_id', '$payment_method', '$bank_account', '$card_type')";
    mysqli_query($conn, $payment_query);

    // Redirect atau tampilkan pesan sukses
    echo "<script>alert('Pesanan berhasil dibuat!'); window.location.href = 'thank_you.php';</script>";
}

mysqli_close($conn);
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
