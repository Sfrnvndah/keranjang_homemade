<?php
    session_start();
    include '../database/connection.php';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $trainingListId = isset($_POST['training_list_id']) ? $_POST['training_list_id'] : null;
    if (!$trainingListId) {
        header("Location: training.php");
        exit;
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM training_list WHERE training_list_id = ?");
        $stmt->execute([$trainingListId]);
        $training = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$training) {
            throw new Exception("Pelatihan tidak ditemukan.");
        }
    } catch (Exception $e) {
        die("Terjadi kesalahan: " . htmlspecialchars($e->getMessage()));
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
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="stylesheet" href="../assets/css/training-customer.css">
        <link rel="stylesheet" href="../assets/css/account.css">
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
                                <li class="scroll-to-section"><a href="../pages/product_lists.php">Product</a></li>
                                <li class="submenu">
                                    <a href="javascript:;">Pages</a>
                                    <ul>
                                        <li><a href="about_us.php">About Us</a></li>
                                        <li><a href="#.php">Training</a></li>
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

        <div class="custom-register-container" style="margin-top: 100px">
            <div class="custom-register-card">
                <h2 class="custom-register-title">Konfirmasi Pendaftaran Pelatihan</h2>
                <div class="custom-register-details">
                    <p><strong>Nama Pelatihan :</strong> <?= htmlspecialchars($training['location']); ?></p>
                    <p><strong>Tanggal :</strong> <?= date("d-m-Y", strtotime($training['date'])); ?></p>
                    <p><strong>Waktu :</strong> <?= htmlspecialchars($training['time']); ?></p>
                    <p><strong>Harga :</strong> <?= "Rp " . number_format($training['price'], 0, ',', '.'); ?></p>
                </div>
                <form action="process_registration.php" method="post" class="custom-register-form">
                    <input type="hidden" name="training_list_id" value="<?= $trainingListId; ?>">
                    <input type="hidden" name="user_id" value="<?= $userId; ?>">
                    <button type="submit" class="custom-register-button">Konfirmasi Pendaftaran</button>
                    <a href="training.php" class="custom-cancel-button">Batal</a>
                </form>
            </div>
        </div>

        <!-- Popup Session Login -->
        <div class="popup-overlay" id="login-popup">
            <div class="popup-content">
                <p>Silahkan login terlebih dahulu.</p>
                <button onclick="closePopup()">Tutup</button>
            </div>
        </div>

        <!-- Register Training Popup -->
        <div class="popup-overlay" id="training-popup">
            <div class="popup-content">
                <p id="popup-message"></p>
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

        <!-- Session login icon -->
        <script>
            // Session keranjang
            document.getElementById('cart-icon').addEventListener('click', function(event) {
                event.preventDefault();
                var userId = <?= json_encode($userId); ?>;
                if (!userId) {
                    document.getElementById('login-popup').style.display = 'block';
                } else {
                    window.location.href = "../pages/cart.php";
                }
            });

            // Session favorite
            document.getElementById('favorite-icon').addEventListener('click', function(event) {
                event.preventDefault();
                var userId = <?= json_encode($userId); ?>;
                if (!userId) {
                    document.getElementById('login-popup').style.display = 'block';
                } else {
                    window.location.href = "../pages/favorite_product.php";
                }
            });

            // Session keranjang
            document.getElementById('account-icon').addEventListener('click', function(event) {
                event.preventDefault();
                var userId = <?= json_encode($userId); ?>;
                if (!userId) {
                    document.getElementById('login-popup').style.display = 'block';
                } else {
                    window.location.href = "../account/account.php";
                }
            });

            // Fungsi untuk menutup popup
            function closePopup() {
                document.getElementById('login-popup').style.display = 'none';
                document.getElementById('cart-popup').style.display = 'none';
                document.getElementById('favorite-popup').style.display = 'none';
            }
        </script>

        <script>
            // Tangani pengiriman form
            document.querySelector('.custom-register-form').addEventListener('submit', async function (event) {
                event.preventDefault(); // Mencegah reload halaman

                const formData = new FormData(this);
                const response = await fetch('process_registration.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                // Tampilkan pesan di popup
                const popup = document.getElementById('training-popup');
                const message = document.getElementById('popup-message');
                message.textContent = result.message;

                // Tentukan warna popup berdasarkan status
                if (result.status === 'success') {
                    popup.style.backgroundColor = '#f0fff4'; // Hijau muda untuk sukses
                } else {
                    popup.style.backgroundColor = '#fff4f0'; // Merah muda untuk error
                }

                popup.style.display = 'block';
            });

            // Fungsi untuk menutup popup
            function closePopup() {
                document.getElementById('training-popup').style.display = 'none';
            }
        </script>
    </body>
</html>
