<?php
    session_start();
    include '../database/connection.php';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
    function addToFavorite($userId, $productId, $pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO favorite_products (user_id, product_id) VALUES (:userId, :productId)");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    $favorites = [];
    if ($userId) {
        try {
            $sql = "
                SELECT p.product_id, p.product_name, p.image_url
                FROM favorite_products f
                JOIN products p ON f.product_id = p.product_id
                WHERE f.user_id = :userId
            ";
            // Tambahkan klausa pencarian
            if (!empty($searchQuery)) {
                $sql .= " AND p.product_name LIKE :searchQuery";
            }
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            if (!empty($searchQuery)) {
                $searchParam = '%' . $searchQuery . '%';
                $stmt->bindParam(':searchQuery', $searchParam, PDO::PARAM_STR);
            }
            $stmt->execute();
            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }

    // Mengambil nama gambar pertama
    if (!empty($favorites)) {
        // Ambil nama gambar dari produk favorit pertama
        $imageUrls = $favorites[0]['image_url']; // Mengambil elemen pertama
        $firstImage = explode(',', $imageUrls)[0];
    } else {
        $firstImage = '';
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
        <title>Pak Tara Craft - Product Lists</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link rel="stylesheet" href="../assets/css/searchbar.css">
        <link rel="stylesheet" href="../assets/css/login.css">
        <link rel="stylesheet" href="../assets/css/account.css">
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
                                    <a href="#" id="favorite-icon">
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

        <section class="section" id="products">
            <div class="container">
                <div class="row">
                    <!-- Search Bar -->
                    <div class="col-lg-4" style="margin-top: 120px;">
                        <form action="" method="GET" class="search-form">
                            <div class="input-group">
                                <input type="text" name="query" class="form-control" placeholder="Cari produk..." aria-label="Cari produk" value="<?= htmlspecialchars($searchQuery) ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Menampilkan produk favorit -->
                    <div class="favorite-products">
                        <?php foreach ($favorites as $favorite): ?>
                            <?php
                            // Pisahkan gambar dan ambil gambar pertama
                            $imageUrls = $favorite['image_url'];
                            $firstImage = explode(',', $imageUrls)[0];
                            ?>
                            <div class="favorite-item">
                                <img src="../assets/images/<?= htmlspecialchars($firstImage); ?>" alt="<?= htmlspecialchars($favorite['product_name']); ?>">
                                <h3><?= htmlspecialchars($favorite['product_name']); ?></h3>
                                <a href="../pages/product_detail.php?product_id=<?= htmlspecialchars($favorite['product_id']); ?>">Lihat Produk</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

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
                            <li><a href="../index.php">Homepage</a></li>
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
                                <li><a href="https://www.instagram.com/paktaracraft?igsh=MTFjaHFtNHo5dTJhOQ=="><i class="fa fa-instagram"></i></a></li>
                                <li><a href="https://wa.me/c/628976374888"><i class="fa fa-whatsapp"></i></a></li>
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
    </body>
</html>
