<?php
    session_start();
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
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
            $price = $product['price'];
            $formatted_price = number_format($price, 0, ',', '.');
            $stock = $product['stock'];
            $category_id = $product['category_id'];
            $image_url = explode(',', $product['image_url']);
            $firstImage = $image_url[0];
        } else {
            echo "Produk tidak ditemukan!";
            $formatted_price = "0";
        }
    } else {
        echo "ID produk tidak tersedia.";
        $formatted_price = "0";
    }

    // Query review
    $query_reviews = "SELECT r.review_text, r.rating, u.username, r.created_at, r.user_id, r.review_id
                  FROM reviews r
                  JOIN users u ON r.user_id = u.user_id
                  WHERE r.product_id = :product_id
                  ORDER BY r.created_at DESC";
    $stmt_reviews = $pdo->prepare($query_reviews);
    $stmt_reviews->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt_reviews->execute();
    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

    $isReviewed = false;
    if ($userId) {
        $query_user_review = "SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND product_id = :product_id";
        $stmt_user_review = $pdo->prepare($query_user_review);
        $stmt_user_review->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt_user_review->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_user_review->execute();
        $isReviewed = $stmt_user_review->fetchColumn() > 0;
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
        <link rel="stylesheet" href="../assets/css/login.css">
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="stylesheet" href="../assets/css/review.css">
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
                            <?php if (count($image_url) > 1): ?>
                            <button class="prev" onclick="changeImage('prev')">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="next" onclick="changeImage('next')">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="right-content">
                            <h4><?= htmlspecialchars($product_name) ?></h4>
                            <span class="price">Rp <?= $formatted_price ?></span>
                            <span style="color: #767676; text-align: justify; display: block;"><?= htmlspecialchars($description) ?></span>
                            <div class="quantity-content">
                                <div class="left-content">
                                    <h6>Jumlah</h6>
                                </div>
                                <div class="right-content">
                                    <div class="quantity buttons_added">
                                        <input type="button" value="-" class="minus">
                                        <input id="quantity" type="number" step="1" min="1" max="<?= $stock ?>" name="quantity" value="1" class="input-text qty text" size="4">
                                        <input type="button" value="+" class="plus">
                                    </div>
                                </div>
                            </div>
                            <div class="total"> 
                                <h4>Total : Rp <strong id="total-price"><?= $formatted_price ?></strong></h4>
                                <div class="button-container">
                                    <button id="add-to-cart-btn" class="btn-cart">Masukkan Ke Keranjang</button>
                                    <form id="checkout-form" method="POST" action="check_out.php" style="display: inline;">
                                        <input type="hidden" name="selected_products" id="selected_products">
                                        <button type="submit" id="checkout-btn" class="btn-checkout">Checkout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="review-section">
                    <h4>Ulasan</h4>

                    <!-- Form Review -->
                    <?php if (!$isReviewed): ?>
                        <div id="review-section">
                            <form id="review-form" method="POST" action="add_review.php">
                                <textarea name="review_text" placeholder="Tulis ulasan Anda..." required></textarea>
                                <div class="rating" id="rating-container">
                                    <span class="star" data-value="1">★</span>
                                    <span class="star" data-value="2">★</span>
                                    <span class="star" data-value="3">★</span>
                                    <span class="star" data-value="4">★</span>
                                    <span class="star" data-value="5">★</span>
                                </div>
                                <input type="hidden" name="rating" id="rating-input" required>
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
                                <button type="button" id="submit-review">Kirim Ulasan</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p>Anda sudah memberikan ulasan untuk produk ini.</p>
                    <?php endif; ?>

                    <!-- Daftar Ulasan -->
                    <div class="reviews">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <strong class="review-username"><?= htmlspecialchars($review['username']) ?></strong>
                                        <!-- Bintang Rating Pindah ke Bawah Nama -->
                                        <div class="review-rating"><?= str_repeat('⭐', $review['rating']) ?></div>
                                        <!-- Add Trash Can Icon for the logged-in user -->
                                        <?php if ($userId && $userId == $review['user_id']): ?>
                                            <a href="delete_review.php?review_id=<?= $review['review_id'] ?>&product_id=<?= $product_id ?>" class="delete-review">
                                                <i class="fa fa-trash" style="color: red; cursor: pointer;"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <p class="review-text"><?= htmlspecialchars($review['review_text']) ?></p>
                                    <small class="review-date"><?= htmlspecialchars($review['created_at']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-reviews">Belum ada ulasan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popup Session Login -->
        <div class="popup-overlay" id="login-popup">
            <div class="popup-content">
                <p>Silahkan login terlebih dahulu.</p>
                <button onclick="closePopup()">Tutup</button>
            </div>
        </div>

        <!-- Popup Keranjang -->
        <div id="cart-popup" class="popup-overlay">
            <div class="popup-content">
                <p>Produk berhasil ditambahkan ke keranjang.</p>
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

        <!-- Fungsi untuk tombol + - -->
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

        <!-- Session Login Icon -->
        <script>
            const userId = <?php echo json_encode($userId); ?>;
            document.getElementById('cart-icon').addEventListener('click', function (event) {
                if (!userId) {
                    event.preventDefault();
                    showPopup();
                }
            });
            document.getElementById('favorite-icon').addEventListener('click', function (event) {
                if (!userId) {
                    event.preventDefault();
                    showPopup();
                }
            });
            document.getElementById('account-icon').addEventListener('click', function (event) {
                if (!userId) {
                    event.preventDefault();
                    showPopup();
                }
            });
            function showPopup() {
                document.getElementById('login-popup').style.display = 'block';
            }
            function closePopup() {
                document.getElementById('login-popup').style.display = 'none';
                document.getElementById('cart-popup').style.display = 'none';
                document.getElementById('favorite-popup').style.display = 'none';
            }
        </script>

        <!-- Menghitung total berdasarkan jumlah -->
        <script>
            const pricePerUnit = <?= $price ?>;
            const quantityInput = document.getElementById('quantity');
            const totalPriceElement = document.getElementById('total-price');
            document.querySelector('.minus').addEventListener('click', () => {
                let quantity = parseInt(quantityInput.value);
                if (quantity > 1) {
                    quantity--;
                    quantityInput.value = quantity;
                    updateTotalPrice(quantity);
                }
            });
            document.querySelector('.plus').addEventListener('click', () => {
                let quantity = parseInt(quantityInput.value);
                const maxStock = parseInt(quantityInput.max);
                if (quantity < maxStock) {
                    quantity++;
                    quantityInput.value = quantity;
                    updateTotalPrice(quantity);
                }
            });
            quantityInput.addEventListener('input', () => {
                let quantity = parseInt(quantityInput.value);
                const maxStock = parseInt(quantityInput.max);
                if (isNaN(quantity) || quantity < 1) {
                    quantityInput.value = 1;
                    quantity = 1;
                } else if (quantity > maxStock) {
                    quantityInput.value = maxStock;
                    quantity = maxStock;
                }
                updateTotalPrice(quantity);
            });
            function updateTotalPrice(quantity) {
                const totalPrice = pricePerUnit * quantity;
                totalPriceElement.textContent = totalPrice.toLocaleString('id-ID');
            }
        </script>

        <!-- Tombol tambah ke keranjang -->
        <script>
            document.getElementById('add-to-cart-btn').addEventListener('click', function () {
                const userId = <?php echo json_encode($userId); ?>;
                if (!userId) {
                    document.getElementById('login-popup').style.display = 'block';
                    return;
                }
                const productId = <?= json_encode($product_id) ?>;
                const quantity = parseInt(document.getElementById('quantity').value);
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'add_to_cart.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            document.getElementById('cart-popup').style.display = 'block';
                        } else {
                            alert(response.message || 'Terjadi kesalahan saat menambahkan produk.');
                        }
                    } else {
                        alert('Gagal menghubungi server. Coba lagi nanti.');
                    }
                };
                xhr.send(`product_id=${productId}&quantity=${quantity}`);
            });
        </script>

        <!-- Tombol checkout -->
        <script>
            document.getElementById('checkout-btn').addEventListener('click', function () {
                const quantity = document.getElementById('quantity').value;
                const productId = <?= json_encode($product_id) ?>;
                const productName = <?= json_encode($product_name) ?>;
                const imageUrl = <?= json_encode($firstImage) ?>;
                const price = <?= json_encode($price) ?>;
                const totalPrice = price * quantity;

                const selectedProducts = [{
                    cart_id: productId,
                    product_name: productName,
                    image_url: imageUrl,
                    price: price,
                    total_price: totalPrice,
                    quantity: quantity
                }];

                document.getElementById('selected_products').value = JSON.stringify(selectedProducts);
            });
        </script>

        <!-- Rating bintang -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const stars = document.querySelectorAll('.star');
                const ratingInput = document.getElementById('rating-input');

                stars.forEach(star => {
                    star.addEventListener('click', () => {
                        const value = star.getAttribute('data-value');
                        ratingInput.value = value;

                        stars.forEach(s => {
                            s.classList.remove('active');
                        });

                        star.classList.add('active');
                        for (let i = 0; i < value; i++) {
                            stars[i].classList.add('active');
                        }
                    });
                });
            });
        </script>
    
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const userId = <?php echo json_encode($userId); ?>;
                const submitReviewButton = document.getElementById('submit-review');
                const loginPopup = document.getElementById('login-popup');
                submitReviewButton.addEventListener('click', () => {
                    if (!userId) {
                        loginPopup.style.display = 'block';
                    } else {
                        document.getElementById('review-form').submit();
                    }
                });
            });
            function closePopup() {
                document.getElementById('login-popup').style.display = 'none';
            }
        </script>
    </body>
</html>