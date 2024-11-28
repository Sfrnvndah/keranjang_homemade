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
        <title>Pak Tara Craft - About Us</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="stylesheet" href="../assets/css/login.css">
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
                                <li class="scroll-to-section"><a href="pages/product_lists.php">Product</a></li>
                                <li class="submenu">
                                    <a href="javascript:;">Pages</a>
                                    <ul>
                                        <li><a href="#">About Us</a></li>
                                        <li><a href="training.php">Training</a></li>
                                    </ul>
                                </li>
                                <!-- icons -->
                                <li class="scroll-to-section">
                                    <a href="../pages/cart.php" id="cart-icon">
                                        <i class="fa fa-shopping-cart" style="font-size: 1.5em; color: #59CB2C;" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li class="scroll-to-section">
                                    <a href="../pages/favorite_product.php" id="favorite-icon">
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

        <div class="about-us" style="margin-top: 150px">
            <div class="container">
                <div class="row">
                    <!-- <div class="col-lg-6">
                        <div class="left-image">
                            <img src="../assets/images/about-us-banner.jpg" alt="">
                        </div>
                    </div> -->
                    <div class="col-lg-6">
                        <div id="map">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3949.1484204671237!2d113.72582507500918!3d-8.18779999184373!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zOMKwMTEnMTYuMSJTIDExM8KwNDMnNDIuMiJF!5e0!3m2!1sid!2sid!4v1732597282363!5m2!1sid!2sid" width="100%" height="400px" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="right-content">
                            <h4>About Us</h4>
                            <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod kon tempor incididunt ut labore.</span>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod kon tempor incididunt ut labore et dolore magna aliqua ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p>
                            <ul>
                                <li><a href="https://www.instagram.com/paktaracraft?igsh=MTFjaHFtNHo5dTJhOQ=="><i class="fa fa-instagram"></i></a></li>
                                <li><a href="https://wa.me/c/628976374888"><i class="fa fa-whatsapp"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="our-team">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-heading">
                            <h2>Our Amazing Team</h2>
                            <span>Kenali para kreator dan tangan-tangan penuh semangat di balik setiap karya handmade.</span>
                        </div>
                    </div>
                    <!-- Safira Novanda Hafizham -->
                    <div class="col-lg-3">
                        <div class="team-item">
                            <div class="thumb">
                                <div class="hover-effect">
                                    <div class="inner-content">
                                        <ul>
                                            <li><a href="https://www.instagram.com/sfrnvndaah/profilecard/?igsh=MTN0MGVpaXlyOGN0Yg=="><i class="fa fa-instagram"></i></a></li>
                                            <li><a href="https://wa.me/qr/H2NJRL5D4FX5I1"><i class="fa fa-whatsapp"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <img src="../assets/images/safira-novanda-hafizham.jpg">
                            </div>
                            <div class="down-content">
                                <h4>Safira Novanda Hafizham</h4>
                                <span>Marketing Specialist</span>
                            </div>
                        </div>
                    </div>
                    <!-- Sefanya Isro'atul Pratiwi -->
                    <div class="col-lg-3">
                        <div class="team-item">
                            <div class="thumb">
                                <div class="hover-effect">
                                    <div class="inner-content">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                            <li><a href="#"><i class="fa fa-whatsapp"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <img src="../assets/images/sefanya-isroatul-pratiwi.jpg">
                            </div>
                            <div class="down-content">
                                <h4>Sefanya Isro'atul Pratiwi</h4>
                                <span>Content Creator</span>
                            </div>
                        </div>
                    </div>
                    <!-- Pingki Sukmawati -->
                    <div class="col-lg-3">
                        <div class="team-item">
                            <div class="thumb">
                                <div class="hover-effect">
                                    <div class="inner-content">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                            <li><a href="#"><i class="fa fa-whatsapp"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <img src="../assets/images/pingki-sukmawati.jpg">
                            </div>
                            <div class="down-content">
                                <h4>Pingki Sukmawati</h4>
                                <span>Product Designer</span>
                            </div>
                        </div>
                    </div>
                    <!-- Muamar Rosyidin -->
                    <div class="col-lg-3">
                        <div class="team-item">
                            <div class="thumb">
                                <div class="hover-effect">
                                    <div class="inner-content">
                                        <ul>
                                            <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                            <li><a href="#"><i class="fa fa-whatsapp"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <img src="../assets/images/muamar-rosyidin.jpg">
                            </div>
                            <div class="down-content">
                                <h4>Muamar Rosyidin</h4>
                                <span>Customer Support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="our-services">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-heading">
                            <h2>Our Services</h2>
                            <span>Detail yang membuat produk kami istimewa!</span>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Custom Basket Design</h4>
                            <p>Kami menawarkan desain keranjang kustom sesuai dengan keinginan dan kebutuhan Anda. Buat keranjang unik untuk berbagai acara.</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Handmade Quality</h4>
                            <p>Setiap keranjang dibuat dengan tangan oleh pengrajin terampil, menjamin kualitas dan ketahanan yang tinggi untuk setiap produk.</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="service-item">
                            <h4>Gift Wrapping Service</h4>
                            <p>Kami menyediakan layanan pembungkusan hadiah dengan desain yang menarik, sempurna untuk kejutan atau hadiah spesial.</p>
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
                            <h2>Membawa Keindahan dan Kualitas ke Setiap Keranjang</h2>
                            <span>Pak Tara Craft adalah tempat di mana keindahan bertemu dengan keterampilan tangan.
                                Kami berkomitmen untuk membuat keranjang-keranjang yang tidak hanya fungsional tetapi juga penuh makna.
                                Setiap keranjang yang kami buat dipenuhi dengan sentuhan cinta dan perhatian, memberikan produk berkualitas tinggi yang sempurna untuk berbagai kebutuhan—baik itu untuk acara khusus, hadiah, atau untuk melengkapi rumah Anda.
                                Dengan desain yang bisa disesuaikan, kami berharap dapat memberikan pengalaman berbelanja yang menyenangkan dan memuaskan.
                                Bergabunglah bersama kami dalam merayakan keindahan kerajinan tangan yang dipenuhi dengan nilai dan kreativitas!
                            </span>
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
                            <li><a href="#">About Us</a></li>
                            <li><a href="training.php">Training</a></li>
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
    </body>
</html>