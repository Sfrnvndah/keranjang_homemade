<?php
    if (!isset($_SESSION)) {
        session_name('admin_session');
        session_start();
    }
    require '../database/connection.php';
    // Cek apakah admin sudah login
    if (!isset($_SESSION['admin_user_id'])) {
        // Redirect ke halaman login jika belum login
        header("Location: ../form/admin_login.php");
        exit;
    }

    // Ambil data admin dari database
    $adminId = $_SESSION['admin_user_id'];
    $stmt = $pdo->prepare("SELECT username, email, password, name, phone FROM users WHERE user_id = :user_id AND level = 1");
    $stmt->execute([':user_id' => $adminId]);
    $admin = $stmt->fetch();

    if (!$admin) {
        echo "Data admin tidak ditemukan.";
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
        <title>Admin Profile</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="stylesheet" href="../assets/css/logout.css">
        <link rel="stylesheet" href="../assets/css/admin-profile.css">
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
    </head>

    <body style="background-color: #fafafa; color: #000;">
        <nav id="sidebar" style="background-color: #00827f;">
            <ul>
                <li>
                    <span class="logo" style="font-size: 16px; font-weight: bold; color: #FFF;">PAK TARA CRAFT</span>
                    <button onclick=toggleSidebar() id="toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/></svg>
                    </button>
                </li>
                <li>
                    <a href="../admin.php">
                        <img src="../assets/images/icon-dashboard.png" alt="Dashboard" width="24px" height="24px">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../admin/product.php">
                        <img src="../assets/images/icon-product.png" alt="Dashboard" width="24px" height="24px">
                        <span>Produk</span>
                    </a>
                </li>
                <li>
                    <a href="../admin/order.php">
                        <img src="../assets/images/icon-order.png" alt="Dashboard" width="24px" height="24px">
                        <span>Pesanan</span>
                    </a>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="../assets/images/icon-report.png" alt="Dashboard" width="24px" height="24px">
                        <span>Laporan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="../report/order.php">Penjualan</a></li>
                        <li><a href="../report/income.php">Pendapatan</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="../assets/images/icon-sales.png" alt="Dashboard" width="24px" height="24px">
                        <span>Penjualan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="../admin/sales_offline.php">Penjualan Offline</a></li>
                        <li><a href="../admin/sales_history.php">Riwayat Penjualan</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <button onclick=toggleSubMenu(this) class="dropdown-btn">
                        <img src="../assets/images/icon-training.png" alt="Dashboard" width="24px" height="24px">
                        <span>Pelatihan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z"/></svg>
                    </button>
                    <ul class="sub-menu">
                    <div>
                        <li><a href="../admin/training_list.php">Daftar Pelatihan</a></li>
                        <li><a href="../admin/participants_list.php">Daftar Peserta</a></li>
                    </div>
                    </ul>
                </li>
                <!-- <li>
                    <a href="../admin/review.php">
                        <img src="../assets/images/icon-review.png" alt="Dashboard" width="24px" height="24px">
                        <span>Review</span>
                    </a>
                </li> -->
                <li class="active">
                    <a href="#">
                        <img src="../assets/images/icon-profile.png" alt="Dashboard" width="24px" height="24px">
                        <span>Profile</span>
                    </a>
                </li>
                <div class="logout-container">
                    <button class="logout-btn" onclick="window.location.href='../form/admin_logout.php'">
                        <span>Logout</span>
                    </button>
                </div>
            </ul>
        </nav>

        <main>
            <div class="admin-profile-container">
                <div class="admin-profile-card">
                    <div class="admin-profile-card-header">Profile</div>
                    <div class="admin-profile-card-body">
                        <table class="admin-profile-table">
                            <tr>
                                <th>Username</th>
                                <td><?php echo htmlspecialchars($admin['username']); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Password</th>
                                <td>
                                    <?php echo str_repeat('•', strlen($admin['password'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td><?php echo htmlspecialchars($admin['name'] ?? ''); ?></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td><?php echo htmlspecialchars($admin['phone'] ?? ''); ?></td>
                            </tr>
                        </table>
                        <div class="admin-profile-btn-container">
                            <button class="admin-profile-btn admin-profile-btn-warning" onclick="openEditPopup()">Edit Profil</button>
                            <button class="admin-profile-btn admin-profile-btn-delete" onclick="openDeleteConfirmation()">Hapus Akun</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popup Konfirmasi Hapus -->
            <div class="popup-overlay" id="popupOverlay" style="display: none;">
                <div class="popup-content">
                    <p id="popupMessage" style="color: black">Apakah Anda yakin ingin menghapus akun ini?</p>
                    <button onclick="deleteAccount()">Ya</button>
                    <button onclick="closePopup()">Batal</button>
                </div>
            </div>

            <!-- Popup for Editing Profile -->
            <div class="edit-popup-overlay" id="editPopupOverlay" style="display: none;">
                <div class="edit-popup">
                    <div class="edit-popup-header">
                        <h3>Edit Profil Admin</h3>
                        <button class="close-popup-btn" onclick="closeEditPopup()">✖</button>
                    </div>
                    <form method="POST" action="update_admin_profile.php">
                        <div class="edit-popup-body">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($admin['password']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($admin['name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="edit-popup-footer">
                            <button type="submit" class="btn-save">Simpan</button>
                            <button type="button" class="btn-cancel" onclick="closeEditPopup()">Batal</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
                <div class="alert alert-success">
                    Profil berhasil diperbarui.
                </div>
            <?php endif; ?>
        </main>

        <script src="../assets/js/line-chart.js"></script>
        <script src="../assets/js/bar-chart.js"></script>
        <script src="../assets/js/year-picker.js"></script>

        <script>
            function closePopup() {
            const popupOverlay = document.getElementById('popupOverlay');
            popupOverlay.style.display = 'none';
        }
        </script>

        <script>
            function openDeleteConfirmation() {
                const popupOverlay = document.getElementById('popupOverlay');
                popupOverlay.style.display = 'flex';
            }
            function closePopup() {
                const popupOverlay = document.getElementById('popupOverlay');
                popupOverlay.style.display = 'none';
            }
            function deleteAccount() {
                window.location.href = 'delete_admin_profile.php';
            }
            function openEditPopup() {
                const popup = document.getElementById("editPopupOverlay");
                popup.style.display = "flex";
            }
            function closeEditPopup() {
                document.getElementById('editPopupOverlay').style.display = 'none';
            }
        </script>
    </body>
</html>