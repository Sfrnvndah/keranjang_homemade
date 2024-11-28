<?php
    session_start();
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
    } else {
        $message = null;
    }
    require '../database/connection.php';
    // Cek apakah user sudah login
    $userId = $_SESSION['user_id'];
    // Ambil data user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $user = $stmt->fetch();
    if ($userId) {
        try {
            // Query untuk mengambil data user dari tabel users
            $stmt = $pdo->prepare("SELECT username, email, name, phone, address, password FROM users WHERE user_id = :userId");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Jika user tidak ditemukan, redirect ke halaman login
            if (!$user) {
                header('Location: ../form/login.php');
                exit;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    } else {
        header('Location: ../form/login.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        try {
            // Cek apakah email sudah digunakan oleh user lain
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :userId");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                echo "<script>alert('Email sudah terdaftar!'); window.history.back();</script>";
                exit;
            }
            // Update informasi user
            $stmt = $pdo->prepare("
                UPDATE users 
                SET name = :name, email = :email, phone = :phone, address = :address
                WHERE user_id = :userId
            ");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':address', $address, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
                echo "<script>alert('Informasi berhasil diperbarui!'); window.location.href='../account/account.php';</script>";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
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
        <title>Pak Tara Craft</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link rel="stylesheet" href="../assets/css/login.css">
        <link rel="stylesheet" href="../assets/css/account.css">
        <link rel="stylesheet" href="../assets/css/popup.css">
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
                                        <li><a href="../content/contact_us.php">Contact Us</a></li>
                                        <li><a href="../content/training.php">Training</a></li>
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
                                    <a href="#" id="account-icon">
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

        <div class="customer-account-container">
            <!-- Header section -->
            <div class="customer-account-header">
                <h1>Akun Saya</h1>
            </div>
            <!-- Informasi akun -->
            <div class="customer-account-details">
                <!-- Informasi pribadi -->
                <div class="customer-info-card">
                    <i class="fa fa-edit edit-icon" onclick="showSection('edit-info')" title="Edit"></i>
                    <h2>Informasi Pribadi</h2>
                    <p><strong>Nama :</strong> <?= htmlspecialchars($user['name']); ?></p>
                    <hr>
                    <p><strong>Email :</strong> <?= htmlspecialchars($user['email']); ?></p>
                    <hr>
                    <p><strong>No. Telepon :</strong> <?= htmlspecialchars($user['phone']); ?></p>
                    <hr>
                    <p><strong>Alamat :</strong> <?= htmlspecialchars($user['address']); ?></p>
                </div>
                <!-- Pengaturan akun -->
                <div class="customer-info-card">
                    <h2>Pengaturan Akun</h2>
                    <form action="edit_info_handler.php" method="POST">
                        <p><strong>Username :</strong> <?= htmlspecialchars($user['username']); ?></p>
                        <p><strong>Password :</strong> <?= str_repeat('*', strlen($user['password'])); ?></p>
                        <a href="javascript:void(0);" onclick="showSection('change-password')">Ganti Password</a>
                    </form>
                </div>
                <!-- Riwayat pesanan -->
                <div class="customer-info-card">
                    <h2>Riwayat Pesanan</h2>
                    <a href="../pages/order_history.php">Lihat Riwayat Pesanan</a>
                </div>
            </div>
            <!-- Modal Edit Informasi -->
            <div id="editInfoModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('editInfoModal')">&times;</span>
                    <h2>Edit Informasi</h2>
                    <form id="editInfoForm" method="post" action="edit_info_handler.php">
                        <label for="name">Nama :</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
                        <label for="email">Email :</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                        <label for="phone">No. Telepon :</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>
                        <label for="address">Alamat :</label>
                        <textarea id="address" name="address" required><?= htmlspecialchars($user['address']); ?></textarea>
                        <button type="submit">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
            <!-- Modal Ganti Password -->
            <div id="changePasswordModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('changePasswordModal')">&times;</span>
                    <h2>Ganti Password</h2>
                    <form id="changePasswordForm" method="post" action="change_password_handler.php">
                        <label for="new_password">Password Baru :</label>
                        <input type="password" id="new_password" name="new_password" required>
                        <label for="confirm_password">Konfirmasi Password :</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="submit">Simpan Password</button>
                    </form>
                </div>
            </div>
            <div id="popupOverlay" class="popup-overlay" style="<?= $message ? 'display: block;' : 'display: none;' ?>">
                <div class="popup-content">
                    <p><?= htmlspecialchars($message); ?></p>
                    <button onclick="closePopup()">Tutup</button>
                </div>
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
            function showSection(sectionId) {
                if (sectionId === 'edit-info') {
                    showModal('editInfoModal');
                } else if (sectionId === 'change-password') {
                    showModal('changePasswordModal');
                }
            }
            function showModal(modalId) {
                document.getElementById(modalId).style.display = "block";
            }

            function closeModal(modalId) {
                document.getElementById(modalId).style.display = "none";
            }
            window.onclick = function(event) {
                const modals = ['editInfoModal', 'changePasswordModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                });
            };
        </script>

        <script>
            function closePopup() {
                const popupOverlay = document.getElementById('popupOverlay');
                popupOverlay.style.display = 'none';
            }
        </script>
    </body>
</html>