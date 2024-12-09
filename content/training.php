<?php
    session_start();
    include '../database/connection.php';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
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

        <section class="section" id="explore" style="margin-top: 40px">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="left-content" style="text-align: justify; display: block;">
                            <h3>Apa itu pelatihan Pak Tara Craft?</h3>
                            <span>Pelatihan Pak Tara Craft adalah program pembelajaran untuk mengasah keterampilan membuat kerajinan tangan, terutama keranjang handmade. Dalam pelatihan ini, peserta akan mempelajari teknik dasar hingga tingkat mahir dalam membuat kerajinan.</span>
                            <p>Pelatihan ini cocok untuk siapa saja, baik pemula yang ingin memulai hobi baru maupun pengrajin berpengalaman yang ingin meningkatkan keahliannya. Bergabunglah bersama kami!</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="right-content">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="first-image">
                                        <img src="../assets/images/pelatihan1.jpg" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="second-image">
                                        <img src="../assets/images/pelatihan2.jpg" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="our-services">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12" style="margin-top: -40px">
                        <div class="section-heading">
                            <h2>Apa yang Akan Kamu Pelajari?</h2>
                            <span>Beragam keterampilan untuk menciptakan kerajinan berkualitas!</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Teknik Dasar</h4>
                            <p>Pelajari cara memilih bahan, alat, dan langkah awal untuk menciptakan keranjang handmade yang kokoh dan menarik.</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Kreativitas & Desain</h4>
                            <p>Kembangkan kreativitas Anda dalam menciptakan desain unik dan personal yang sesuai dengan kebutuhan atau tema tertentu.</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Finishing Berkualitas</h4>
                            <p>Pelajari teknik finishing seperti pewarnaan, pelapisan, dan dekorasi untuk memastikan hasil akhir kerajinan terlihat profesional.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="custom-training-container mt-5">
                <h2 class="custom-training-title text-center mb-4" style="margin-top: -20px;">Daftar Pelatihan</h2>
                <table class="custom-training-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Lokasi</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query menggunakan PDO
                        try {
                            $stmt = $pdo->query("SELECT * FROM training_list ORDER BY date, time");
                            $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($trainings)):
                                $no = 1;
                                foreach ($trainings as $training):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date("d-m-Y", strtotime($training['date'])); ?></td>
                            <td><?= htmlspecialchars($training['time']); ?></td>
                            <td><?= htmlspecialchars($training['location']); ?></td>
                            <td><?= "Rp " . number_format($training['price'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($userId): ?>
                                    <!-- User login: Form Daftar -->
                                    <form action="register_training.php" method="post">
                                        <input type="hidden" name="training_list_id" value="<?= $training['training_list_id']; ?>">
                                        <button type="submit" class="custom-training-button">Daftar</button>
                                    </form>
                                <?php else: ?>
                                    <!-- User belum login: Tombol memunculkan popup -->
                                    <button type="button" class="custom-training-button" onclick="showLoginPopup()">Daftar</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                                endforeach;
                            else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pelatihan yang tersedia</td>
                        </tr>
                        <?php endif; ?>
                        <?php
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='6' class='text-center'>Terjadi kesalahan: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Popup Session Login -->
        <div class="popup-overlay" id="login-popup">
            <div class="popup-content">
                <p>Silahkan login terlebih dahulu.</p>
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
            // Fungsi untuk menampilkan popup "Silahkan login terlebih dahulu"
            function showLoginPopup() {
                document.getElementById('login-popup').style.display = 'block';
            }
            // Fungsi untuk menutup popup
            function closePopup() {
                document.getElementById('login-popup').style.display = 'none';
            }
        </script>
    </body>
</html>