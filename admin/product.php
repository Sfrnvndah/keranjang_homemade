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
    $search = $_GET['search'] ?? '';
    try {
        $query = "
            SELECT 
                products.product_id, 
                products.product_name, 
                products.description, 
                products.price, 
                products.stock, 
                categories.category_name, 
                products.image_url
            FROM products
            INNER JOIN categories ON products.category_id = categories.category_id
        ";
        if (!empty($search)) {
            $query .= " WHERE 
                        products.product_name LIKE :search 
                        OR categories.category_name LIKE :search
                        OR products.stock LIKE :search 
                        OR products.price = :priceExact";
        }
        $stmt = $pdo->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            if (is_numeric(str_replace('.', '', $search))) {
                $exactPrice = str_replace('.', '', $search);
                $stmt->bindValue(':priceExact', $exactPrice, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':priceExact', -1, PDO::PARAM_INT);
            }
        }
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Produk</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="stylesheet" href="../assets/css/training.css">
        <link rel="stylesheet" href="../assets/css/logout.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
    </head>

    <body>
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
                <li class="active">
                    <a href="#">
                        <img src="../assets/images/icon-product.png" alt="Dashboard" width="24px" height="24px">
                        <span>Produk</span>
                    </a>
                </li>
                <li>
                    <a href="order.php">
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
                        <li><a href="sales_offline.php">Penjualan Offline</a></li>
                        <li><a href="sales_history.php">Riwayat Penjualan</a></li>
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
                        <li><a href="training_list.php">Daftar Pelatihan</a></li>
                        <li><a href="participants_list.php">Daftar Peserta</a></li>
                    </div>
                    </ul>
                </li>
                <!-- <li>
                    <a href="review.php">
                        <img src="../assets/images/icon-review.png" alt="Dashboard" width="24px" height="24px">
                        <span>Review</span>
                    </a>
                </li> -->
                <li>
                    <a href="../account/profile.php">
                        <img src="../assets/images/icon-profile.png" alt="Dashboard" width="24px" height="24px">
                        <span>Profile</span>
                    </a>
                </li>
                <div class="logout-container">
                    <button class="logout-btn" onclick="window.location.href='../account/logout.php'">
                        <span>Logout</span>
                    </button>
                </div>
            </ul>
        </nav>

        <main style="background-color: white">
            <section>
                <h2>Daftar Produk</h2>
                <form method="GET" style="margin-bottom: -20px; display: flex; align-items: center; justify-content: flex-start; gap: 10px; width: 100%;">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Cari produk..." 
                        value="<?= htmlspecialchars($search) ?>" 
                        style="width: 300px; padding: 5px; font-size: 13px; border: 1px solid #ddd; border-radius: 5px;">
                    <button type="submit" style="padding: 10px 20px; font-size: 13px; border: none; background-color: #00827f; color: white; border-radius: 5px; cursor: pointer; margin-bottom: 15px;">Cari</button>
                    <a href="add_product.php" style="padding: 10px 20px; font-size: 13px; border: none; background-color: #00827f; color: white; border-radius: 5px; cursor: pointer; text-decoration: none; margin-bottom: 15px; margin-left: auto;">Tambah Produk</a>
                </form>
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center;">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Nama</th>
                            <th style="text-align: center;">Deskripsi</th>
                            <th style="text-align: center;">Harga</th>
                            <th style="text-align: center;">Stok</th>
                            <th style="text-align: center;">Kategori</th>
                            <th style="text-align: center;">Gambar</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 10px;">
                        <?php foreach ($products as $product): ?>
                            <?php
                                $images = explode(",", $product['image_url']); // Pisahkan gambar berdasarkan koma
                                $firstImage = trim($images[0]); // Ambil gambar pertama
                            ?>
                            <tr>
                                <td style="text-align: center;"><?= htmlspecialchars($product['product_name']) ?></td>
                                <td style="text-align: justify;"><?= htmlspecialchars($product['description']) ?></td>
                                <td style="text-align: center;">Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($product['stock']) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($product['category_name']) ?></td>
                                <td>
                                    <img src="../assets/images/<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" style="width: 100px; height: auto; cursor: pointer;" onclick="openPopup('<?= htmlspecialchars($product['image_url']) ?>')">
                                </td>   
                                <td style="text-align: center;">
                                    <a href="edit_product.php?id=<?= $product['product_id'] ?>">Edit</a>
                                    <!-- <a href="delete_product.php?id=<?= $product['product_id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</a> -->
                                    <a href="javascript:void(0);" 
                                        onclick="openConfirmationPopup('<?= $product['product_id'] ?>', '<?= htmlspecialchars($product['product_name']) ?>')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <div class="popup-overlay">
                <div class="popup-content">
                    <p style="color: black; font-size: 16px; text-align: center;">Apakah Anda yakin ingin menghapus item ini?</p>
                </div>
            </div>

            <div class="popup-overlay" id="popup-success">
                <div class="popup-content">
                    <p id="popup-message" style="color: black; font-size: 16px; text-align: center;"></p>
                    <button onclick="closePopup()">Tutup</button>
                </div>
            </div>
        </main>

        <script>
            function openConfirmationPopup(productId, productName) {
                const popupOverlay = document.querySelector('.popup-overlay');
                const popupContent = popupOverlay.querySelector('.popup-content p');
                popupContent.innerHTML = `
                    Apakah Anda yakin ingin menghapus produk <b>${productName}</b>?
                    <br>
                    <button onclick="deleteProduct(${productId})" style="margin-top: 10px; padding: 5px 10px; background-color: red; color: white; border: none; border-radius: 5px; cursor: pointer;">Hapus</button>
                    <button onclick="closePopup()" style="margin-top: 10px; padding: 5px 10px; background-color: gray; color: white; border: none; border-radius: 5px; cursor: pointer;">Batal</button>
                `;
                popupOverlay.style.display = 'block';
            }

            function closePopup() {
                const popupOverlay = document.querySelector('.popup-overlay');
                popupOverlay.style.display = 'none';
            }

            function deleteProduct(productId) {
                // Redirect ke halaman delete dengan ID produk
                window.location.href = `delete_product.php?id=${productId}`;
            }
        </script>

        <script>
            // Fungsi untuk mendapatkan parameter dari URL
            function getURLParameter(name) {
                const params = new URLSearchParams(window.location.search);
                return params.get(name);
            }
            // Fungsi untuk menampilkan popup
            function showPopup(message) {
                const popup = document.getElementById('popup-success');
                popup.style.display = 'flex';
                document.getElementById('popup-message').textContent = message;
            }
            // Fungsi untuk menutup popup
            function closePopup() {
                document.getElementById('popup-success').style.display = 'none';
            }
            // Ambil parameter dari URL
            const status = getURLParameter('status');
            const message = getURLParameter('message');
            // Jika ada parameter, tampilkan popup
            if (status && message) {
                showPopup(decodeURIComponent(message)); // Decode untuk menghilangkan %20
            }
        </script>
    </body>
</html>