<?php
    include '../database/connection.php';
    if (isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
        $query = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product_name = $product['product_name'];
            $description = $product['description'];
            $price = number_format($product['price'], 0, ',', '.');
            $stock = $product['stock'];
            $category_id = $product['category_id'];
            $image_url = explode(',', $product['image_url']);
            $firstImage = $image_url[0];
        } else {
            echo "Produk tidak ditemukan!";
        }
    } else {
        echo "ID produk tidak tersedia.";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Product Detail - <?= htmlspecialchars($product_name) ?></title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
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
                                        <li><a href="check_out.php">Check Out</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:;">Pages</a>
                                    <ul>
                                        <li><a href="../content/about_us.php">About Us</a></li>
                                        <li><a href="../content/contact_us.php">Contact Us</a></li>
                                        <li><a href="../content/training.php">Training</a></li>
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

        <div class="page-heading" id="top">
            <div class="container"></div>
        </div>

        <section class="section" id="product">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="left-images">
                            <div class="image-container">
                                <?php foreach ($image_url as $index => $image): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($product_name) ?>" class="product-image" data-index="<?= $index ?>" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($image_url) > 1): ?> <!-- Cek jika ada lebih dari satu gambar -->
                            <button class="prev" onclick="changeImage('prev')">
                                <img src="../assets/images/prev.png" alt="Previous">
                            </button>
                            <button class="next" onclick="changeImage('next')">
                                <img src="../assets/images/next.png" alt="Next">
                            </button>
                        <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="right-content">
                            <h4><?= htmlspecialchars($product_name) ?></h4>
                            <span class="price">Rp<?= $price ?></span>
                            <span><?= htmlspecialchars($description) ?></span>
                            <div class="quote">
                                <i class="fa fa-quote-left"></i><p>Stock: <?= $stock ?> units</p>
                            </div>
                            <div class="quantity-content">
                                <div class="left-content">
                                    <h6>No. of Orders</h6>
                                </div>
                                <div class="right-content">
                                    <div class="quantity buttons_added">
                                        <input type="button" value="-" class="minus">
                                        <input type="number" step="1" min="1" max="<?= $stock ?>" name="quantity" value="1" class="input-text qty text" size="4">
                                        <input type="button" value="+" class="plus">
                                    </div>
                                </div>
                            </div>
                            <div class="total">
                                <h4>Total: Rp<?= number_format($stock * $price, 0, ',', '.') ?></h4>
                                <div class="main-border-button"><a href="#">Add To Cart</a></div>
                            </div>
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

        <script src="../assets/js/jquery-2.1.0.min.js"></script>
        <script src="../assets/js/bootstrap.min.js"></script>
        <script>
            let currentImageIndex = 0;
            const images = document.querySelectorAll('.product-image');
            function changeImage(direction) {
                const totalImages = images.length;
                images[currentImageIndex].style.display = 'none';
                if (direction === 'next') {
                    currentImageIndex = (currentImageIndex + 1) % totalImages;
                } else {
                    currentImageIndex = (currentImageIndex - 1 + totalImages) % totalImages;
                }
                images[currentImageIndex].style.display = 'block';
            }
        </script>

    </body>
</html>
