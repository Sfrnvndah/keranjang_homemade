<?php
    session_start();
    include 'database/connection.php';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $currentYear = date('Y');
    // Query keranjang kecil
    $querykeranjangkecil = "SELECT product_id, product_name, image_url, price
                        FROM products 
                        WHERE category_id = 1 AND stock > 0
                        ORDER BY product_id DESC 
                        LIMIT 6";
    $stmtkecil = $pdo->prepare($querykeranjangkecil);
    $stmtkecil->execute();
    // Query keranjang sedang
    $querykeranjangsedang = "SELECT product_id, product_name, image_url, price
                             FROM products 
                             WHERE category_id = 2 AND stock > 0
                             ORDER BY product_id DESC 
                             LIMIT 6";
    $stmtsedang = $pdo->prepare($querykeranjangsedang);
    $stmtsedang->execute();
    // QUery keranjang besar
    $querykeranjangbesar = "SELECT product_id, product_name, image_url, price
                            FROM products 
                            WHERE category_id = 3 AND stock > 0
                            ORDER BY product_id DESC 
                            LIMIT 6";
    $stmtbesar = $pdo->prepare($querykeranjangbesar);
    $stmtbesar->execute();
    // Query keranjang jumbo
    $querykeranjangjumbo = "SELECT product_id, product_name, image_url, price
                            FROM products 
                            WHERE category_id = 4 AND stock > 0
                            ORDER BY product_id DESC 
                            LIMIT 6";
    $stmtjumbo = $pdo->prepare($querykeranjangjumbo);
    $stmtjumbo->execute();
    // Format decimal ke rupiah
    function formatRupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }

    // Menampilkan produk yang paling banyak terjual
    // $queryBestSeller = "
    //     SELECT p.product_name, p.image_url, SUM(oi.quantity) + SUM(oi_offline.quantity) AS total_quantity
    //     FROM products p
    //     LEFT JOIN order_items oi ON p.product_id = oi.product_id
    //     LEFT JOIN orders o ON oi.order_id = o.order_id AND YEAR(o.order_date) = :currentYear
    //     LEFT JOIN offline_order_items oi_offline ON p.product_id = oi_offline.product_id
    //     LEFT JOIN offline_orders o_offline ON oi_offline.offline_order_id = o_offline.offline_order_id AND YEAR(o_offline.order_date) = :currentYear
    //     WHERE p.stock > 0
    //     GROUP BY p.product_id
    //     ORDER BY total_quantity DESC
    //     LIMIT 1
    // ";
    // $stmtBestSeller = $pdo->prepare($queryBestSeller);
    // $stmtBestSeller->bindParam(':currentYear', $currentYear, PDO::PARAM_INT);
    // $stmtBestSeller->execute();
    // $bestSeller = $stmtBestSeller->fetch(PDO::FETCH_ASSOC);
    // if ($bestSeller) {
    //     $productName = $bestSeller['product_name'];
    //     $imageUrls = explode(',', $bestSeller['image_url']);
    //     $imageUrl = "assets/images/" . $imageUrls[0];
    // } else {
    //     $productName = "Produk Tidak Ditemukan";
    //     $imageUrl = "assets/images/default.jpg";
    // }

    // Menampilkan produk secara acak
    $queryUniqueFetch = "SELECT product_id, product_name, image_url 
        FROM products 
        WHERE stock > 0 
        ORDER BY RAND() 
        LIMIT 1";
    $stmtUniqueFetch = $pdo->prepare($queryUniqueFetch);
    $stmtUniqueFetch->execute();
    $productDataUnique = $stmtUniqueFetch->fetch(PDO::FETCH_ASSOC);
    // Jika produk ditemukan
    if ($productDataUnique) {
        // Ambil data produk
        $productIdUnique = htmlspecialchars($productDataUnique['product_id']);
        $productNameUnique = htmlspecialchars($productDataUnique['product_name']);
        // Ambil gambar pertama jika ada lebih dari satu gambar
        $imageListUnique = explode(',', $productDataUnique['image_url']);
        $imageFileNameUnique = trim($imageListUnique[0]); // Gambar pertama
        $imageUrlUnique = "assets/images/" . htmlspecialchars($imageFileNameUnique);

        // Menampilkan keranjang kecil yang paling banyak terjual
        $queryCategoryOneBestSeller = "
            SELECT p.product_name, p.image_url, SUM(oi.quantity) + SUM(oi_offline.quantity) AS total_quantity
            FROM products p
            LEFT JOIN order_items oi ON p.product_id = oi.product_id
            LEFT JOIN orders o ON oi.order_id = o.order_id AND YEAR(o.order_date) = :yearCategoryOne
            LEFT JOIN offline_order_items oi_offline ON p.product_id = oi_offline.product_id
            LEFT JOIN offline_orders o_offline ON oi_offline.offline_order_id = o_offline.offline_order_id AND YEAR(o_offline.order_date) = :yearCategoryOne
            WHERE p.stock > 0 AND p.category_id = 1
            GROUP BY p.product_id
            ORDER BY total_quantity DESC
            LIMIT 1
        ";
        $stmtCategoryOne = $pdo->prepare($queryCategoryOneBestSeller);
        $stmtCategoryOne->bindParam(':yearCategoryOne', $currentYear, PDO::PARAM_INT);
        $stmtCategoryOne->execute();
        $categoryOneBestSeller = $stmtCategoryOne->fetch(PDO::FETCH_ASSOC);
        if ($categoryOneBestSeller) {
            $productNameCategoryOne = $categoryOneBestSeller['product_name'];
            $imageUrlsOne = explode(',', $categoryOneBestSeller['image_url']);
            $imageUrlCategoryOne = "assets/images/" . $imageUrlsOne[0];
        } else {
            $productNameCategoryOne = "Produk Tidak Ditemukan";
            $imageUrlCategoryOne = "assets/images/default.jpg";
        }

    // Menampilkan keranjang sedang yang paling banyak terjual
    $queryCategoryTwoBestSeller = "
        SELECT p.product_name, p.image_url, SUM(oi.quantity) + SUM(oi_offline.quantity) AS total_quantity
        FROM products p
        LEFT JOIN order_items oi ON p.product_id = oi.product_id
        LEFT JOIN orders o ON oi.order_id = o.order_id AND YEAR(o.order_date) = :yearCategoryTwo
        LEFT JOIN offline_order_items oi_offline ON p.product_id = oi_offline.product_id
        LEFT JOIN offline_orders o_offline ON oi_offline.offline_order_id = o_offline.offline_order_id AND YEAR(o_offline.order_date) = :yearCategoryTwo
        WHERE p.stock > 0 AND p.category_id = 2
        GROUP BY p.product_id
        ORDER BY total_quantity DESC
        LIMIT 1
    ";
    $stmtCategoryTwo = $pdo->prepare($queryCategoryTwoBestSeller);
    $stmtCategoryTwo->bindParam(':yearCategoryTwo', $currentYear, PDO::PARAM_INT);
    $stmtCategoryTwo->execute();
    $categoryTwoBestSeller = $stmtCategoryTwo->fetch(PDO::FETCH_ASSOC);
    if ($categoryTwoBestSeller) {
        $productNameCategoryTwo = $categoryTwoBestSeller['product_name'];
        $imageUrlsTwo = explode(',', $categoryTwoBestSeller['image_url']);
        $imageUrlCategoryTwo = "assets/images/" . $imageUrlsTwo[0];
    } else {
        $productNameCategoryTwo = "Produk Tidak Ditemukan";
        $imageUrlCategoryTwo = "assets/images/default.jpg";
    }

    // Menampilkan keranjang besar yang paling banyak terjual
    $queryCategoryThreeBestSeller = "
        SELECT p.product_name, p.image_url, SUM(oi.quantity) + SUM(oi_offline.quantity) AS total_quantity
        FROM products p
        LEFT JOIN order_items oi ON p.product_id = oi.product_id
        LEFT JOIN orders o ON oi.order_id = o.order_id AND YEAR(o.order_date) = :yearCategoryThree
        LEFT JOIN offline_order_items oi_offline ON p.product_id = oi_offline.product_id
        LEFT JOIN offline_orders o_offline ON oi_offline.offline_order_id = o_offline.offline_order_id AND YEAR(o_offline.order_date) = :yearCategoryThree
        WHERE p.stock > 0 AND p.category_id = 3
        GROUP BY p.product_id
        ORDER BY total_quantity DESC
        LIMIT 1
    ";
    $stmtCategoryThree = $pdo->prepare($queryCategoryThreeBestSeller);
    $stmtCategoryThree->bindParam(':yearCategoryThree', $currentYear, PDO::PARAM_INT);
    $stmtCategoryThree->execute();
    $categoryThreeBestSeller = $stmtCategoryThree->fetch(PDO::FETCH_ASSOC);
    if ($categoryThreeBestSeller) {
        $productNameCategoryThree = $categoryThreeBestSeller['product_name'];
        $imageUrlsThree = explode(',', $categoryThreeBestSeller['image_url']);
        $imageUrlCategoryThree = "assets/images/" . $imageUrlsThree[0];
    } else {
        $productNameCategoryThree = "Produk Tidak Ditemukan";
        $imageUrlCategoryThree = "assets/images/default.jpg";
    }

    // Menampilkan keranjang jumbo yang paling banyak terjual
    $queryCategoryFourBestSeller = "
        SELECT p.product_name, p.image_url, SUM(oi.quantity) + SUM(oi_offline.quantity) AS total_quantity
        FROM products p
        LEFT JOIN order_items oi ON p.product_id = oi.product_id
        LEFT JOIN orders o ON oi.order_id = o.order_id AND YEAR(o.order_date) = :yearCategoryFour
        LEFT JOIN offline_order_items oi_offline ON p.product_id = oi_offline.product_id
        LEFT JOIN offline_orders o_offline ON oi_offline.offline_order_id = o_offline.offline_order_id AND YEAR(o_offline.order_date) = :yearCategoryFour
        WHERE p.stock > 0 AND p.category_id = 4
        GROUP BY p.product_id
        ORDER BY total_quantity DESC
        LIMIT 1
    ";
    $stmtCategoryFour = $pdo->prepare($queryCategoryFourBestSeller);
    $stmtCategoryFour->bindParam(':yearCategoryFour', $currentYear, PDO::PARAM_INT);
    $stmtCategoryFour->execute();
    $categoryFourBestSeller = $stmtCategoryFour->fetch(PDO::FETCH_ASSOC);
    if ($categoryFourBestSeller) {
        $productNameCategoryFour = $categoryFourBestSeller['product_name'];
        $imageUrlsFour = explode(',', $categoryFourBestSeller['image_url']);
        $imageUrlCategoryFour = "assets/images/" . $imageUrlsFour[0];
    } else {
        $productNameCategoryFour = "Produk Tidak Ditemukan";
        $imageUrlCategoryFour = "assets/images/default.jpg";
    }

    // Memasukkan produk ke keranjang
    if (isset($_GET['add_to_cart']) && $userId) {
        $productId = (int)$_GET['add_to_cart'];
        try {
            // Cek apakah produk sudah ada di keranjang
            $queryCheckCart = "SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id";
            $stmtCheckCart = $pdo->prepare($queryCheckCart);
            $stmtCheckCart->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            if ($stmtCheckCart->rowCount() > 0) {
                // Jika sudah ada, update jumlah produk
                $queryUpdateCart = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = :user_id AND product_id = :product_id";
                $stmtUpdateCart = $pdo->prepare($queryUpdateCart);
                $stmtUpdateCart->execute([
                    ':user_id' => $userId,
                    ':product_id' => $productId
                ]);
            } else {
                // Jika produk belum ada di keranjang, tambahkan ke cart
                $queryAddCart = "INSERT INTO cart (user_id, product_id, quantity, added_at) 
                                 VALUES (:user_id, :product_id, 1, NOW())";
                $stmtAddCart = $pdo->prepare($queryAddCart);
                $stmtAddCart->execute([
                    ':user_id' => $userId,
                    ':product_id' => $productId
                ]);
            }
            // Menampilkan popup setelah berhasil menambah produk ke keranjang
            echo "<script>
                    document.getElementById('cart-popup').style.display = 'block';
                  </script>";
        } catch (PDOException $e) {
            echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "');</script>";
        }
    }

    // Menambahkan produk ke favorit
    if (isset($_GET['add_to_favorite']) && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $productId = $_GET['add_to_favorite'];
        // Periksa apakah produk sudah ada di favorit
        $checkQuery = "SELECT * FROM favorite_products WHERE user_id = :user_id AND product_id = :product_id";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([':user_id' => $userId, ':product_id' => $productId]);
        // Jika produk belum ada di favorit, tambahkan
        if ($checkStmt->rowCount() == 0) {
            $insertQuery = "INSERT INTO favorite_products (user_id, product_id) VALUES (:user_id, :product_id)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([':user_id' => $userId, ':product_id' => $productId]);
        }
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
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
        <link rel="stylesheet" href="assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="assets/css/owl-carousel.css">
        <link rel="stylesheet" href="assets/css/lightbox.css">
        <link rel="stylesheet" href="assets/css/login.css">
        <link rel="stylesheet" href="assets/css/popup.css">
    </head>
    
    <body>
        <div id="preloader">
            <div class="jumper">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>

        <header class="header-area header-sticky">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="main-nav">
                            <a href="index.php" class="logo">
                                <img src="assets/images/pak-tara-craft-logo-black-no-background.png">
                            </a>
                            <ul class="nav">
                                <li class="scroll-to-section"><a href="#top" class="active">Home</a></li>
                                <li class="submenu">
                                    <a href="javascript:;">Product</a>
                                    <ul>
                                        <li><a href="pages/product_lists.php">Product Lists</a></li>
                                        <li><a href="pages/check_out.php">Check Out</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:;">Pages</a>
                                    <ul>
                                        <li><a href="content/about_us.php">About Us</a></li>
                                        <li><a href="content/contact_us.php">Contact Us</a></li>
                                        <li><a href="content/training.php">Training</a></li>
                                    </ul>
                                </li>
                                <li class="scroll-to-section">
                                    <a href="pages/cart.php"><i class="fa fa-shopping-cart" style="font-size: 1.5em; color: #00827f;" aria-hidden="true"></i></a>
                                </li>
                                <li class="scroll-to-section">
                                    <a href="account/account.php" id="account-icon">
                                        <i class="fa fa-user" style="font-size: 1.5em; color: #00827f;" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <!-- Tombol Login atau Logout -->
                                <?php if ($userId): ?>
                                    <form action="form/logout.php" method="post" style="display: inline;">
                                        <button type="submit" class="btn-logout">Logout</button>
                                    </form>
                                <?php else: ?>
                                    <a href="form/login.php" class="btn-login">Login</a>
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

        <!-- Popup Session Login -->
        <div class="popup-overlay" id="login-popup">
            <div class="popup-content">
                <p>Silahkan login terlebih dahulu.</p>
                <button onclick="closePopup()">Tutup</button>
            </div>
        </div>

        <div class="main-banner" id="top">
            <div class="container-fluid">
                <div class="row">
                    <!-- Menampilkan produk -->
                    <div class="col-lg-6">
                        <div class="left-content">
                            <div class="thumb">
                                <div class="inner-content">
                                    <h3><?= $productNameUnique; ?></h3>
                                    <div class="main-border-button">
                                        <a href="pages/product_detail.php?product_id=<?= $productIdUnique; ?>">Lihat</a>
                                    </div>
                                </div>
                                <img src="<?= $imageUrlUnique; ?>" alt="<?= $productNameUnique; ?>">
                            </div>
                        </div>
                    </div>
                    <?php
                    } else {
                        echo "<p>Produk tidak tersedia.</p>";
                    }
                    ?>
                    <!-- Menampilkan produk terlaris berdasarkan ukuran -->
                    <div class="col-lg-6">
                        <div class="right-content">
                            <div class="row">
                                <!-- Keranjang kecil terlaris -->
                                <div class="col-lg-6">
                                    <div class="right-first-image">
                                        <div class="thumb">
                                            <div class="inner-content">
                                                <h4>Keranjang Kecil</h4>
                                                <span>Produk kategori 1 yang paling laris tahun ini</span>
                                            </div>
                                            <div class="hover-content">
                                                <div class="inner">
                                                    <h4>Keranjang Kecil</h4>
                                                    <p>Pas untuk keperluan kecil dan praktis sehari-hari.</p>
                                                    <div class="main-border-button">
                                                        <a href="pages/product_lists.php?query=&category%5B%5D=1">Temukan Lebih Banyak</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <img src="<?= htmlspecialchars($imageUrlCategoryOne); ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- Keranjang sedang terlaris -->
                                <div class="col-lg-6">
                                    <div class="right-first-image">
                                        <div class="thumb">
                                            <div class="inner-content">
                                                <h4>Keranjang Sedang</h4>
                                                <span>Cocok untuk jalan-jalan, dapat menyimpan lebih banyak barang</span>
                                            </div>
                                            <div class="hover-content">
                                                <div class="inner">
                                                    <h4>Keranjang Sedang</h4>
                                                    <p>Ukuran sedang yang cocok untuk menyimpan handuk, alat tulis, atau barang sehari-hari. Keranjang ini serbaguna dan mudah ditempatkan di berbagai sudut ruangan.</p>
                                                    <div class="main-border-button">
                                                        <a href="pages/product_lists.php?query=&category%5B%5D=2">Temukan Lebih Banyak</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <img src="<?= htmlspecialchars($imageUrlCategoryTwo); ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- Keranjang besar terlaris -->
                                <div class="col-lg-6">
                                    <div class="right-first-image">
                                        <div class="thumb">
                                            <div class="inner-content">
                                                <h4>Keranjang Besar</h4>
                                                <span>Ideal untuk menyimpan lebih banyak barang.</span>
                                            </div>
                                            <div class="hover-content">
                                                <div class="inner">
                                                    <h4>Keranjang Besar</h4>
                                                    <p>Dengan kapasitas besar, keranjang ini cocok untuk menyimpan pakaian, mainan, atau barang-barang yang lebih besar. Pilihan tepat untuk kamar tidur atau ruang keluarga.</p>
                                                    <div class="main-border-button">
                                                        <a href="pages/product_lists.php?query=&category%5B%5D=3">Temukan Lebih Banyak</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <img src="<?= htmlspecialchars($imageUrlCategoryThree); ?>">
                                        </div>
                                    </div>
                                </div>
                                <!-- Keranjang jumbo terlaris -->
                                <div class="col-lg-6">
                                    <div class="right-first-image">
                                        <div class="thumb">
                                            <div class="inner-content">
                                                <h4>Keranjang Jumbo</h4>
                                                <span>Kapasitas besar, sempurna untuk berbagai keperluan</span>
                                            </div>
                                            <div class="hover-content">
                                                <div class="inner">
                                                    <h4>Keranjang Jumbo</h4>
                                                    <p>Uuntuk kebutuhan penyimpanan ekstra besar seperti pakaian kotor, selimut, atau mainan dalam jumlah banyak. Sempurna untuk rumah dengan ruang penyimpanan besar.</p>
                                                    <div class="main-border-button">
                                                        <a href="pages/product_lists.php?query=&category%5B%5D=4">Temukan Lebih Banyak</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <img src="<?= htmlspecialchars($imageUrlCategoryFour); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keranjang Kecil -->
        <section class="section" id="women">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="section-heading">
                            <h2>Keranjang Kecil Terbaru</h2>
                            <span>Desain unik, cocok untuk kebutuhan sehari-hari.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="women-item-carousel">
                            <div class="owl-women-item owl-carousel">
                                <?php while ($row = $stmtkecil->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <div class="item">
                                        <div class="thumb">
                                            <div class="hover-content">
                                                <ul>
                                                    <li>
                                                        <a href="pages/product_detail.php?product_id=<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-eye" style="color: #3B95E4"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="add-to-cart" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-shopping-cart" style="color: #59CB2C"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="add-to-favorite" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-heart" style="color: #ff4d4d"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <?php
                                                $images = explode(",", $row['image_url']);
                                                $first_image = htmlspecialchars(trim($images[0] ?? 'default.jpg'));
                                            ?>
                                            <img src="assets/images/<?= $first_image; ?>" alt="<?= htmlspecialchars($row['product_name']); ?>">
                                        </div>
                                        <div class="down-content">
                                            <h4><?= htmlspecialchars($row['product_name']); ?></h4>
                                            <span><?= formatRupiah($row['price']); ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Keranjang Sedang -->
        <section class="section" id="women">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="section-heading">
                            <h2>Keranjang Sedang Terbaru</h2>
                            <span>Cocok untuk jalan-jalan.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="women-item-carousel">
                            <div class="owl-women-item owl-carousel">
                                <?php while ($row = $stmtsedang->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <div class="item">
                                        <div class="thumb">
                                            <div class="hover-content">
                                                <ul>
                                                    <li>
                                                        <a href="pages/product_detail.php?product_id=<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-eye" style="color: #3B95E4"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="add-to-cart" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-shopping-cart" style="color: #59CB2C"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="add-to-favorite" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-heart" style="color: #ff4d4d"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <?php
                                                $images = explode(",", $row['image_url']);
                                                $first_image = htmlspecialchars(trim($images[0] ?? 'default.jpg'));
                                            ?>
                                            <img src="assets/images/<?= $first_image; ?>" alt="<?= htmlspecialchars($row['product_name']); ?>">
                                        </div>
                                        <div class="down-content">
                                            <h4><?= htmlspecialchars($row['product_name']); ?></h4>
                                            <span><?= formatRupiah($row['price']); ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Keranjang Besar -->
        <section class="section" id="women">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="section-heading">
                            <h2>Keranjang Besar Terbaru</h2>
                            <span>Ideal untuk menyimpan lebih banyak barang.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="women-item-carousel">
                            <div class="owl-women-item owl-carousel">
                                <?php while ($row = $stmtbesar->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <div class="item">
                                        <div class="thumb">
                                            <div class="hover-content">
                                                <ul>
                                                    <li>
                                                        <a href="pages/product_detail.php?product_id=<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-eye" style="color: #3B95E4"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="add-to-cart" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-shopping-cart" style="color: #59CB2C"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="add-to-favorite" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-heart" style="color: #ff4d4d"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <?php
                                                $images = explode(",", $row['image_url']);
                                                $first_image = htmlspecialchars(trim($images[0] ?? 'default.jpg'));
                                            ?>
                                            <img src="assets/images/<?= $first_image; ?>" alt="<?= htmlspecialchars($row['product_name']); ?>">
                                        </div>
                                        <div class="down-content">
                                            <h4><?= htmlspecialchars($row['product_name']); ?></h4>
                                            <span><?= formatRupiah($row['price']); ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Keranjang Jumbo -->
        <section class="section" id="women">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="section-heading">
                            <h2>Keranjang Jumbo Terbaru</h2>
                            <span>Kapasitas besar, sempurna untuk berbagai keperluan.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="women-item-carousel">
                            <div class="owl-women-item owl-carousel">
                                <?php while ($row = $stmtjumbo->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <div class="item">
                                        <div class="thumb">
                                            <div class="hover-content">
                                                <ul>
                                                    <li>
                                                        <a href="pages/product_detail.php?product_id=<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-eye" style="color: #3B95E4"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="add-to-cart" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-shopping-cart" style="color: #59CB2C"></i>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" class="add-to-favorite" data-product-id="<?= htmlspecialchars($row['product_id']); ?>">
                                                            <i class="fa fa-heart" style="color: #ff4d4d"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <?php
                                                $images = explode(",", $row['image_url']);
                                                $first_image = htmlspecialchars(trim($images[0] ?? 'default.jpg'));
                                            ?>
                                            <img src="assets/images/<?= $first_image; ?>" alt="<?= htmlspecialchars($row['product_name']); ?>">
                                        </div>
                                        <div class="down-content">
                                            <h4><?= htmlspecialchars($row['product_name']); ?></h4>
                                            <span><?= formatRupiah($row['price']); ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popup Keranjang -->
        <div id="cart-popup" class="popup-overlay">
            <div class="popup-content">
                <p>Produk berhasil ditambahkan ke keranjang.</p>
                <button onclick="closePopup()">Tutup</button>
            </div>
        </div>

        <div id="favorite-popup" class="popup-overlay">
            <div class="popup-content">
                <p>Produk berhasil ditambahkan ke favorit.</p>
                <button onclick="closePopup()">Tutup</button>
            </div>
        </div>

        <section class="section" id="explore">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="left-content">
                            <h2>Jelajahi Produk Kami</h2>
                            <span>Selamat datang di Pak Tara Craft! Temukan berbagai keranjang anyaman handmade yang unik dan berkualitas tinggi. Setiap keranjang kami dibuat dengan penuh ketelitian dan keahlian, menggunakan bahan-bahan alami yang ramah lingkungan.</span>
                            <div class="quote">
                                <i class="fa fa-quote-left"></i><p>Apa kamu mencari keranjang untuk berbagai kebutuhan? Kami memiliki berbagai pilihan yang cocok untukmu.</p>
                            </div>
                            <p>Setiap produk di Pak Tara Craft adalah hasil karya tangan yang mengedepankan keindahan, ketahanan, dan kegunaan. Kami berkomitmen untuk memberikan produk terbaik untuk mendukung gaya hidup Anda yang lebih alami dan berkelanjutan.</p>
                            <p>Terima kasih telah memilih Pak Tara Craft. Kami harap keranjang kami dapat membawa kehangatan dan keindahan ke dalam kehidupan kamu!</p>
                            <div class="main-border-button">
                                <a href="pages/products_lists.php">Temukan Lebih Banyak</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="right-content">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="leather">
                                        <h4>Keranjang Lucu</h4>
                                        <span>Dibuat dengan sentuhan cinta</span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="first-image">
                                        <img src="assets/images/blooming-elegance.jpg" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="second-image">
                                        <img src="assets/images/natural-harmony.jpg" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="types">
                                        <h4>Tipe Berbeda-Beda</h4>
                                        <span>Temukan koleksi yang unik</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="subscribe">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="section-heading">
                            <h2>Temukan Keranjang Anyaman yang Tepat untuk kamu!</h2>
                            <span>Keindahan ada pada setiap detail, dan kami di Pak Tara Craft percaya bahwa setiap keranjang anyaman memiliki cerita dan keunikannya sendiri. Temukan berbagai pilihan keranjang handmade yang tidak hanya berguna, tetapi juga membawa sentuhan seni yang unik untuk rumah kamu.</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-6">
                                <ul>
                                    <li style="color: black; font-weight: bold;">Lokasi Toko :<br><span>Gg. Melon, Pelindu, Karangrejo, Kec. Sumbersari, Kabupaten Jember, Jawa Timur 68124, Indonesia</span></li>
                                    <li style="color: black; font-weight: bold;">Nomor Telepon :<br><span>+62 897-6374-888</span></li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul>
                                    <li class="info-label" style="color: black; font-weight: bold;">Jam Buka :<br><span>24 Jam</span></li>
                                    <li class="info-label" style="color: black; font-weight: bold;">Email :<br><span>lidyaningrum8379@gmail.com</span></li>
                                    <li class="info-label" style="color: black; font-weight: bold;">Social Media :<br>
                                        <span>
                                            <a href="https://www.instagram.com/paktaracraft?igsh=MTFjaHFtNHo5dTJhOQ==">Instagram</a>,
                                            <a href="https://www.tiktok.com/@paktaracraft1705_83?_t=8rHExNw65VY&_r=1">TikTok</a>,
                                            <a href="https://wa.me/c/628976374888">WhatsApp</a>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="first-item">
                            <div class="logo">
                                <img src="assets/images/pak-tara-craft-logo-white-no-background.png" alt="hexashop ecommerce templatemo">
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
                            <li><a href="#">Homepage</a></li>
                            <li><a href="content/about_us.php">About Us</a></li>
                            <li><a href="content/contact_us.php">Contact Us</a></li>
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
        <script src="assets/js/jquery-2.1.0.min.js"></script>

        <!-- Bootstrap -->
        <script src="assets/js/popper.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>

        <!-- Plugins -->
        <script src="assets/js/owl-carousel.js"></script>
        <script src="assets/js/accordions.js"></script>
        <script src="assets/js/datepicker.js"></script>
        <script src="assets/js/scrollreveal.min.js"></script>
        <script src="assets/js/waypoints.min.js"></script>
        <script src="assets/js/jquery.counterup.min.js"></script>
        <script src="assets/js/imgfix.min.js"></script> 
        <script src="assets/js/slick.js"></script> 
        <script src="assets/js/lightbox.js"></script> 
        <script src="assets/js/isotope.js"></script> 
        
        <!-- Global Init -->
        <script src="assets/js/custom.js"></script>

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

        <!-- Fungsi untuk menambah produk ke keranjang -->
        <script>
            document.querySelectorAll('.add-to-cart').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    var userId = <?= json_encode($userId); ?>;
                    var productId = this.getAttribute('data-product-id');
                    // Jika user belum login, tampilkan popup login
                    if (!userId) {
                        document.getElementById('login-popup').style.display = 'block';
                        return;
                    }
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', 'index.php?add_to_cart=' + productId, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById('cart-popup').style.display = 'block';
                        } else {
                            alert('Terjadi kesalahan saat menambah produk ke keranjang');
                        }
                    };
                    xhr.send();
                });
            });

            document.querySelectorAll('.add-to-favorite').forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        var userId = <?= json_encode($userId); ?>;
        var productId = this.getAttribute('data-product-id');
        
        // Jika user belum login, tampilkan popup login
        if (!userId) {
            document.getElementById('login-popup').style.display = 'block';
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'index.php?add_to_favorite=' + productId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('favorite-popup').style.display = 'block';
            } else {
                alert('Terjadi kesalahan saat menambah produk ke favorit');
            }
        };
        xhr.send();
    });
});

            function closePopup() {
                document.getElementById('login-popup').style.display = 'none';
                document.getElementById('cart-popup').style.display = 'none';
                document.getElementById('favorite-popup').style.display = 'none';
            }
        </script>
    </body>
</html>