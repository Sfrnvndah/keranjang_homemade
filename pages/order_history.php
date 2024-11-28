<?php
    session_start();
    require '../database/connection.php';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if (!$userId) {
        die("Anda harus login untuk melihat riwayat pesanan.");
    }
    // $cari_pesanan = isset($_POST['cari_pesanan']) ? $_POST['cari_pesanan'] : '';
    $sqlPesanan = "
        SELECT 
            o.order_id AS id,
            o.order_date AS tanggal,
            o.total_amount AS total,
            o.status AS status
        FROM orders o
        WHERE o.user_id = :userId
        ORDER BY o.order_date DESC
    ";
    try {
        $stmt = $pdo->prepare($sqlPesanan);
        $stmt->execute([':userId' => $userId]);
        $resultPesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    if (isset($_POST['order_id'])) {
        $orderId = $_POST['order_id'];
        $sqlDetailPesanan = "
            SELECT 
                p.product_name AS produk,
                oi.quantity AS jumlah,
                oi.quantity * p.price AS subtotal,
                o.order_date AS order_date  -- Add order date here
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            JOIN orders o ON oi.order_id = o.order_id  -- Join with orders table to get the order date
            WHERE oi.order_id = :orderId
        ";
        $stmt = $pdo->prepare($sqlDetailPesanan);
        $stmt->execute([':orderId' => $orderId]);
        $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['details' => $orderDetails, 'order_date' => $orderDetails[0]['order_date']]);
        exit;
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
        <title>Pak Tara Craft</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link rel="stylesheet" href="../assets/css/login.css">
        <link rel="stylesheet" href="../assets/css/order-history.css">
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

        <div class="container">
            <h2 style="text-align: center; color: #333; margin-bottom: 60px; margin-top: 120px">Daftar Riwayat Pesanan</h2>

            <!-- Form Cari Pesanan -->
            <!-- <div class="search-bar-container">
                <form method="POST" action="">
                    <input type="text" name="cari_pesanan" placeholder="Cari pesanan..." value="<?= htmlspecialchars($cari_pesanan); ?>">
                    <button type="submit">Cari</button>
                </form>
            </div> -->

            <!-- Tabel Riwayat Pesanan -->
            <table class="table table-centered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status Pesanan</th>
                        <th>View Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($resultPesanan)): ?>
                        <?php foreach ($resultPesanan as $index => $pesanan): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td><?= htmlspecialchars($pesanan['tanggal']); ?></td>
                                <td>Rp <?= number_format($pesanan['total'], 0, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($pesanan['status']); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="loadDetails(<?= $pesanan['id']; ?>)">View Details</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Tidak ada riwayat pesanan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="order-details" class="mt-4" style="display: none;">
            <h2 style="text-align: center; color: #333; margin-top: 80px; margin-bottom: 20px;">
                Detail Pesanan Tanggal <?= !empty($resultPesanan) ? date('d F Y', strtotime($resultPesanan[0]['tanggal'])) : 'Belum ada riwayat pesanan'; ?>
            </h2>
                <table class="table table-centered table-striped">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="details-body"></tbody>
                </table>
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
                            <li><a href="#">Keranjang Kecil</a></li>
                            <li><a href="#">Keranjang Sedang</a></li>
                            <li><a href="#">Keranjang Besar</a></li>
                            <li><a href="#">Keranjang Jumbo</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><a href="../index">Homepage</a></li>
                            <li><a href="../content/about_us.php">About Us</a></li>
                            <li><a href="../content/training.php">Training</a></li>
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
        
        <!-- jQuery -->
        <script src="../assets/js/jquery-2.1.0.min.js"></script>

        <!-- Bootstrap -->
        <script src="../assets/js/popper.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>

        <!-- Plugins -->
        <script src="../assets/js/owl-carousel.js"></script>
        <script src="../assets/js/accordions.js"></script>
        <script src="../assets/js/datepicker.js"></script>
        <script src="../assets/js/scrollreveal.min.js"></script>
        <script src="../assets/js/waypoints.min.js"></script>
        <script src="../assets/js/jquery.counterup.min.js"></script>
        <script src="../assets/js/imgfix.min.js"></script> 
        <script src="../assets/js/slick.js"></script> 
        <script src="../assets/js/lightbox.js"></script> 
        <script src="../assets/js/isotope.js"></script> 
        
        <!-- Global Init -->
        <script src="../assets/js/custom.js"></script>

        <script>
            $(function() {
                var selectedClass = "";
                $("p").click(function(){
                selectedClass = $(this).attr("data-rel");
                $("#portfolio").fadeTo(50, 0.1);
                    $("#portfolio div").not("."+selectedClass).fadeOut();
                setTimeout(function() {
                $("."+selectedClass).fadeIn();
                $("#portfolio").fadeTo(50, 1);
                }, 500);
                });
            });
        </script>

        <script>
            function loadDetails(orderId) {
                fetch('', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `order_id=${orderId}`
                })
                .then(response => response.json())
                .then(data => {
                    const detailsBody = document.getElementById('details-body');
                    const orderDetails = data.details;
                    const orderDate = data.order_date;
                    const orderDateFormatted = new Date(orderDate).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    document.getElementById('order-details').querySelector('h2').innerText = `Detail Pesanan Tanggal ${orderDateFormatted}`;
                    detailsBody.innerHTML = '';
                    orderDetails.forEach(item => {
                        const row = `<tr>
                            <td>${item.produk}</td>
                            <td>${item.jumlah}</td>
                            <td>Rp${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                        detailsBody.innerHTML += row;
                    });
                    document.getElementById('order-details').style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
            }
        </script>
    </body>
</html>