<?php
    include '../database/connection.php';
    $searchQuery = isset($_GET['query']) ? $_GET['query'] : '';
    $selectedCategories = isset($_GET['category']) ? $_GET['category'] : [];
    $query = "SELECT * FROM products";
    $params = [];
    if ($searchQuery) {
        $query .= " WHERE product_name LIKE ?";
        $params[] = '%' . $searchQuery . '%';
    }
    if (!empty($selectedCategories)) {
        if ($searchQuery) {
            $query .= " AND category_id IN (" . implode(',', array_fill(0, count($selectedCategories), '?')) . ")";
        } else {
            $query .= " WHERE category_id IN (" . implode(',', array_fill(0, count($selectedCategories), '?')) . ")";
        }
        $params = array_merge($params, $selectedCategories);
    }
    $limit = 15;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1);
    $offset = ($page - 1) * $limit;
    $totalProductsQuery = "SELECT COUNT(*) FROM products";
    if ($searchQuery || !empty($selectedCategories)) {
        $totalProductsQuery .= " WHERE product_name LIKE ?";
        if (!empty($selectedCategories)) {
            $totalProductsQuery .= " AND category_id IN (" . implode(',', array_fill(0, count($selectedCategories), '?')) . ")";
        }
        $totalProductsQuery = $pdo->prepare($totalProductsQuery);
        $totalProductsQuery->execute(array_merge(['%' . $searchQuery . '%'], $selectedCategories));
    } else {
        $totalProductsQuery = $pdo->prepare($totalProductsQuery);
        $totalProductsQuery->execute();
    }
    $totalProducts = $totalProductsQuery->fetchColumn();
    $totalPages = ceil($totalProducts / $limit);
    $query .= " LIMIT $limit OFFSET $offset";
    $stmt2 = $pdo->prepare($query);
    $stmt2->execute($params);
    $categoryQuery = "SELECT * FROM categories";
    $categories = $pdo->query($categoryQuery)->fetchAll(PDO::FETCH_ASSOC);
    if (!$categories) {
        $categories = [];
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
    </head>
    
    <body>
        <header class="header-area header-sticky">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="main-nav">
                            <a href="../index.php" class="logo">
                                <img src="../assets/images/logo.png">
                            </a>
                            <ul class="nav">
                                <li class="scroll-to-section"><a href="../index.php">Home</a></li>
                                <li class="submenu">
                                    <a href="javascript:;">Product</a>
                                    <ul>
                                        <li><a href="product_lists.php">Product Lists</a></li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">3</a></li>
                                        <li><a href="#">4</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:;">Pages</a>
                                    <ul>
                                        <li><a href="../content/about_us.php">About Us</a></li>
                                        <li><a href="../content/contact_us.php">Contact Us</a></li>
                                        <li><a href="#">3</a></li>
                                        <li><a href="#">4</a></li>
                                    </ul>
                                </li>
                                <li class="scroll-to-section">
                                    <a href="../pages/cart.php"><i class="fa fa-shopping-cart" style="font-size: 1.5em;" aria-hidden="true"></i></a>
                                </li>
                                <li class="scroll-to-section">
                                    <a href="../account/account.php"><i class="fa fa-user" style="font-size: 1.5em;" aria-hidden="true"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <section class="section" id="products">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Web Title -->
                        <div class="section-heading">
                            <h2>PRODUK</h2>
                            <span>Lihat semua produk kami.</span>
                        </div>
                    </div>
                    <!-- Search Bar -->
                    <div class="col-lg-4">
                        <form action="" method="GET" class="search-form">
                            <div class="input-group">
                                <input type="text" name="query" class="form-control" placeholder="Cari produk..." aria-label="Cari produk" value="<?= htmlspecialchars($searchQuery) ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Filter Form -->
                    <div class="col-lg-6 mb-2">
                        <form action="" method="GET" class="filter-form">
                            <input type="hidden" name="query" value="<?= htmlspecialchars($searchQuery) ?>">
                            <div class="form-group">
                                <?php foreach ($categories as $category): ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="category[]" value="<?= $category['category_id'] ?>" class="form-check-input" id="category<?= $category['category_id'] ?>" <?= in_array($category['category_id'], $selectedCategories) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="category<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i></button>
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <?php while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) : 
                            $images = explode(',', $row['image_url']);
                            $firstImage = $images[0];
                        ?>
                        <div class="col-lg-4">
                            <div class="item">
                                <div class="thumb">
                                    <div class="hover-content">
                                        <ul>
                                            <li><a href="single-product.php?product_id=<?= htmlspecialchars($row['product_id']) ?>"><i class="fa fa-eye"></i></a></li>
                                            <li><a href="cart.php?add=<?= htmlspecialchars($row['product_id']) ?>"><i class="fa fa-shopping-cart"></i></a></li>
                                        </ul>
                                    </div>
                                    <img src="../assets/images/<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
                                </div>
                                <div class="down-content">
                                    <h4><?= htmlspecialchars($row['product_name']) ?></h4>
                                    <span>Rp<?= number_format($row['price'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="col-lg-12">
                        <div class="pagination">
                            <ul>
                                <?php if ($page > 1): ?>
                                    <li><a href="?page=<?= $page - 1 ?>&query=<?= urlencode($searchQuery) ?>&category[]=<?= implode('&category[]=', $selectedCategories) ?>"><</a></li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="<?= ($i === $page) ? 'active' : '' ?>"><a href="?page=<?= $i ?>&query=<?= urlencode($searchQuery) ?>&category[]=<?= implode('&category[]=', $selectedCategories) ?>"><?= $i ?></a></li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li><a href="?page=<?= $page + 1 ?>&query=<?= urlencode($searchQuery) ?>&category[]=<?= implode('&category[]=', $selectedCategories) ?>">></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
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
                                <img src="assets/images/white-logo.png" alt="hexashop ecommerce templatemo">
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
