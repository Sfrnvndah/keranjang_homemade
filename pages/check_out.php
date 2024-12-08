<?php
    session_start();
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $products = []; // Inisialisasi variabel $products sebagai array kosong
    $total_amount = 0; // Inisialisasi total

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['selected_products'])) {
            $selectedProducts = json_decode($_POST['selected_products'], true); // Decode JSON menjadi array

            if (!empty($selectedProducts)) {
                // Format harga dan total untuk semua produk
                foreach ($selectedProducts as $product) {
                    $product_id = $product['cart_id'];
                    $product_name = $product['product_name'];
                    $image_url = $product['image_url'];
                    $price = $product['price'];
                    $total_price = $product['total_price'];
                    $quantity = $product['quantity'];

                    // Format harga
                    $formatted_price = number_format($price, 0, ',', '.');
                    $formatted_total_price = number_format($total_price, 0, ',', '.');

                    // Simpan data produk untuk ditampilkan
                    $products[] = [
                        'image_url' => $image_url,
                        'product_name' => $product_name,
                        'formatted_price' => $formatted_price,
                        'quantity' => $quantity,
                        'total_price' => $total_price, // Simpan total_price untuk perhitungan
                        'formatted_total_price' => $formatted_total_price,
                    ];

                    // Tambahkan total harga dari produk yang sudah dihitung
                    $total_amount += $total_price; // Jumlahkan total harga produk
                }
            } else {
                echo "Tidak ada data produk.";
                exit;
            }
        } else {
            echo "Tidak ada data produk.";
            exit;
        }
    } else {
        echo "Tidak ada data produk.";
        exit;
    }

    include '../database/connection.php';

    // Ambil data user dari database
    $userData = null;
    if ($userId) {
        try {
            $stmt = $pdo->prepare("SELECT name, phone, address, email FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching user data: " . $e->getMessage());
        }
    }

    // Biaya tambahan
    $shipping_cost = 15000; // Biaya pengiriman Cargo
    $service_fee = 1000; // Biaya layanan
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Check Out</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/login.css">
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="stylesheet" href="../assets/css/review.css">
        <link rel="stylesheet" href="../assets/css/check-out.css">
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
    </head>

    <body>
        <header class="header-area header-sticky">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="main-nav">
                            <a href="../index.php" class="logo">
                                <img src="../assets/images/pak-tara-craft-logo-black-no-background.png">
                            </a>
                            <ul class="nav">
                                <li class="scroll-to-section"><a href="../index.php">Home</a></li>
                                <li class="scroll-to-section"><a href="product_lists.php">Product</a></li>
                                <li class="submenu">
                                    <a href="javascript:;">Pages</a>
                                    <ul>
                                        <li><a href="../content/about_us.php">About Us</a></li>
                                        <li><a href="../content/training.php">Training</a></li>
                                    </ul>
                                </li>
                                <!-- icons -->
                                <li class="scroll-to-section">
                                    <a href="cart.php" id="cart-icon">
                                        <i class="fa fa-shopping-cart" style="font-size: 1.5em; color: #59CB2C;" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li class="scroll-to-section">
                                    <a href="favorite_product.php" id="favorite-icon">
                                        <i class="fa fa-heart" style="font-size: 1.5em; color: #ff4d4d;" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li class="scroll-to-section">
                                    <a href="../account/account.php" id="account-icon">
                                        <i class="fa fa-user" style="font-size: 1.5em; color: #00827f;" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <!-- Tombol Login atau Logout -->
                                <?php if ($userId): ?>
                                    <form action="../form/logout.php" method="post" style="display: inline;">
                                        <button type="submit" class="btn-logout">Logout</button>
                                    </form>
                                <?php else: ?>
                                    <a href="../form/login.php" class="btn-login">Login</a>
                                <?php endif; ?>
                            </ul>
                            <!-- Session ID user -->
                            <?php if ($userId): ?>
                                <span style="color: white; font-size: 10px;">
                                    <?= htmlspecialchars($userId); ?>
                                </span>
                            <?php else: ?>
                                <span style="color: white; font-size: 10px;">
                                    User belum login
                                </span>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-heading" id="top">
            <div class="container"></div>
        </div>

        <div class="checkout-container">
            <div class="checkout-header">
                <h2>Checkout</h2>
            </div>

            <!-- Tabel Detail Produk -->
            <div class="product-table-container">
                <h3>Detail Produk</h3>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga Satuan</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <img src="../assets/images/<?= htmlspecialchars($product['image_url']) ?>" 
                                        alt="<?= htmlspecialchars($product['product_name']) ?>" 
                                        class="product-image-table">
                                </td>
                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                <td>Rp <?= $product['formatted_price'] ?></td> <!-- Harga satuan -->
                                <td><?= htmlspecialchars($product['quantity']) ?></td>
                                <td>Rp <?= $product['formatted_total_price'] ?></td> <!-- Subtotal -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><strong>Total</strong></td>
                            <td><strong>Rp <?= number_format($total_amount, 0, ',', '.') ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Informasi Pelanggan -->
            <div class="customer-info">
                <h3>Informasi Pelanggan</h3>
                <div class="info-item">
                    <strong>Nama :</strong>
                    <p><?= htmlspecialchars($userData['name']) ?></p>
                    <hr>
                </div>
                <div class="info-item">
                    <strong>Alamat :</strong>
                    <p><?= nl2br(htmlspecialchars($userData['address'])) ?></p>
                    <hr>
                </div>
                <div class="info-item">
                    <strong>Nomor Telepon :</strong>
                    <p><?= htmlspecialchars($userData['phone']) ?></p>
                    <hr>
                </div>
                <div class="info-item">
                    <strong>Email :</strong>
                    <p><?= htmlspecialchars($userData['email']) ?></p>
                    <hr>
                </div>
            </div>

            <!-- Pengiriman -->
            <div class="order-summary">
                <h5 class="section-title" style="margin-bottom: 15px;">Pengiriman</h5>
                <p><strong>Cargo : </strong>Rp <?php echo number_format($shipping_cost, 0, ',', '.'); ?></p>
                <small class="text-muted">Garansi tiba : 3-5 Hari Kerja</small>
            </div>

            <!-- Rincian Pembayaran -->
            <div class="order-summary">
                <h5 class="section-title" style="margin-bottom: 15px">Rincian Pembayaran</h5>
                <table class="table">
                    <tr>
                        <td>Subtotal Produk</td>
                        <td class="text-end">Rp <?= number_format($total_amount, 0, ',', '.') ?></td>
                    </tr>
                    <tr>
                        <td>Subtotal Pengiriman</td>
                        <td class="text-end">Rp <?php echo number_format($shipping_cost, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td>Biaya Layanan</td>
                        <td class="text-end">Rp <?php echo number_format($service_fee, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <th>Total Pembayaran</th>
                        <th class="text-end">Rp <?= number_format($total_amount + $shipping_cost + $service_fee, 0, ',', '.') ?></th>
                    </tr>
                </table>
            </div>

            <!-- Informasi Pembayaran -->
            <div class="payment-info card">
                <h3 class="payment-title">Metode Pembayaran</h3>
                <div class="payment-method">
                    <h4><i class="fa fa-university"></i> Transfer Bank</h4>
                    <ul class="bank-details">
                        <li>
                            <span class="label">Bank</span> 
                            <span class="colon">:</span> 
                            <span class="value">BCA</span>
                        </li>
                        <li>
                            <span class="label">Nomor Rekening</span> 
                            <span class="colon">:</span> 
                            <span class="value">098765432112</span>
                        </li>
                        <li>
                            <span class="label">Nama Pemilik</span> 
                            <span class="colon">:</span> 
                            <span class="value">Pak Tara Craft</span>
                        </li>
                    </ul>
                </div>
                <div class="payment-method">
                    <h4><i class="fa fa-qrcode"></i>QRIS</h4>
                    <img src="../assets/images/qris.png" alt="QRIS" class="qris-image">
                    <p class="small-text">Atau gunakan QRIS untuk melakukan pembayaran</p>
                </div>
            </div>

            <!-- Upload bukti pembayaran -->
            <div class="payment-info card">
                <h2 class="payment-title">Upload Bukti Pembayaran</h2>
                
                <!-- Form Pesanan -->
                <form id="checkout-form" method="POST" action="order_process.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="proof_of_payment">Bukti Pembayaran <span class="required">*</span></label>
                        <input type="file" name="proof_of_payment" id="proof_of_payment" accept="image/*" required>
                        <small class="help-text">Unggah gambar bukti pembayaran dalam format JPG, PNG, atau JPEG.</small>
                    </div>
                    
                    <input type="hidden" name="selected_products" value="<?= htmlspecialchars(json_encode($selectedProducts)) ?>">
                    <input type="hidden" name="total_amount" value="<?= htmlspecialchars($total_amount + $shipping_cost + $service_fee) ?>">
                    
                    <!-- Tombol Pesan -->
                    <button type="submit" id="submit-button" class="btn-submit">Pesan</button>
                </form>
            </div>
        </div>

        <!-- Pop-up Peringatan -->
        <div class="popup-overlay" id="checkout-popup" style="display: none;">
            <div class="popup-content">
                <p>Upload bukti pembayaran terlebih dahulu!</p>
                <button onclick="closePopup()">Tutup</button>
            </div>
        </div>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="first-item">
                            <div class="logo">
                                <img src="../assets/images/pak-tara-craft-logo-white-no-background.png" alt="hexashop ecommerce templatemo">
                            </div>
                            <ul>
                                <li><a href="https://www.google.co.id/maps/place/Gg.+Melon,+Pelindu,+Karangrejo,+Kec.+Sumbersari,+Kabupaten+Jember,+Jawa+Timur+68124/@-8.1905765,113.7204516,14z/data=!4m6!3m5!1s0x2dd696708d1bdf53:0x186a95d951b7d20b!8m2!3d-8.1877199!4d113.7282515!16s%2Fg%2F1q62d1ll9?entry=ttu&g_ep=EgoyMDI0MTEwNi4wIKXMDSoASAFQAw%3D%3D">Gg. Melon, Pelindu, Karangrejo, Kec. Sumbersari, Kabupaten Jember, Jawa Timur 68124, Indonesia</a></li>
                                <li><a href="#">lidyaningrum8379@gmail.com</a></li>
                                <li><a href="https://wa.me/c/628976374888">+62 897-6374-888</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <h4>Shopping &amp; Categories</h4>
                        <ul>
                            <li><a href="product_lists.php?query=&category%5B%5D=1">Keranjang Kecil</a></li>
                            <li><a href="product_lists.php?query=&category%5B%5D=2">Keranjang Sedang</a></li>
                            <li><a href="product_lists.php?query=&category%5B%5D=3">Keranjang Besar</a></li>
                            <li><a href="product_lists.php?query=&category%5B%5D=4">Keranjang Jumbo</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><a href="#">Homepage</a></li>
                            <li><a href="../content/about_us.php">About Us</a></li>
                            <li><a href="../content/contact_us.php">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <h4>Useful Information</h4>
                        <ul>
                            <li><a href="#">Customer Support</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Terms of Service</a></li>
                            <li><a href="#">Training Information</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-12">
                        <div class="under-footer">
                            <p>Copyright © 2024 Pak Tara Craft., Ltd. All Rights Reserved. 
                            <ul>
                                <li>
                                    <a href="https://www.instagram.com/paktaracraft?igsh=MTFjaHFtNHo5dTJhOQ==">
                                        <i class="fa fa-instagram"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://wa.me/c/628976374888">
                                        <i class="fa fa-whatsapp"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <script src="../assets/js/jquery-2.1.0.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>

        <script>
            // Fungsi untuk menutup pop-up
            function closePopup() {
                document.getElementById('checkout-popup').style.display = 'none';
            }

            // Validasi sebelum mengirim form
            document.getElementById('submit-button').addEventListener('click', function () {
                const fileInput = document.getElementById('proof_of_payment');
                
                if (!fileInput.files || fileInput.files.length === 0) {
                    // Tampilkan pop-up jika file belum diunggah
                    document.getElementById('checkout-popup').style.display = 'block';
                } else {
                    // Kirim form jika file sudah diunggah
                    document.getElementById('checkout-form').submit();
                }
            });
        </script>
    </body>
</html>
