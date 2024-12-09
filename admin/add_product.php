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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productName = $_POST['product_name'];
        $price = str_replace('.', '', $_POST['price']);  // Hapus titik format rupiah
        $price = (float) $price;  // Ubah harga menjadi format float
        $stock = $_POST['stock'];
        $categoryId = $_POST['category_id'];
        $description = $_POST['description'];

        // Mengelola file gambar
        $imageUrls = [];
            if (isset($_FILES['new_image'])) {
                foreach ($_FILES['new_image']['name'] as $key => $imageName) {
                    $imageTmpName = $_FILES['new_image']['tmp_name'][$key];
                    $uploadDir = '../assets/images/';
                    $newImagePath = $uploadDir . $imageName;

                    // Pindahkan file ke folder tujuan
                    if (move_uploaded_file($imageTmpName, $newImagePath)) {
                        $imageUrls[] = $imageName; // Tambahkan nama file asli ke array
                    } else {
                        echo "Gagal mengunggah gambar: $imageName<br>";
                    }
                }
            }

        // Gabungkan nama gambar menjadi string, dipisahkan dengan koma
        $imageUrlsString = implode(',', $imageUrls);

        try {
            // Menyimpan data produk ke database
            $query = "INSERT INTO products (product_name, description, price, stock, category_id, image_url) 
                      VALUES (:product_name, :description, :price, :stock, :category_id, :image_url)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':product_name', $productName);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':stock', $stock);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':image_url', $imageUrlsString);
            $stmt->execute();
            echo "Produk berhasil ditambahkan!";
            header("Location: product.php");
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Ambil data kategori dari database
    try {
        $query = "SELECT category_id, category_name FROM categories";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <title>Tambah Produk</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
        <link rel="stylesheet" href="../assets/css/popup.css">
        <link rel="stylesheet" href="../assets/css/training.css">
        <link rel="stylesheet" href="../assets/css/logout.css">
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
                <li>
                    <a href="product.php">
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

        <main style="padding: 20px;">
        <div class="edit-product-form">
            <h1 class="edit-product-header" style="margin-top: 20px; margin-bottom: 60px">Tambah Produk</h1>
            <form method="POST" enctype="multipart/form-data" onsubmit="clearRupiahFormat()">
                <div style="display: flex; gap: 2rem; align-items: center;">
                    <div>
                        <label for="product_name">Nama Produk :</label>
                        <input type="text" name="product_name" required style="width: 15rem;">
                    </div>
                    <div>
                        <label for="price">Harga :</label>
                        <input
                            type="text" 
                            name="price" 
                            id="price"
                            style="width: 15rem;" 
                            required 
                            oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label for="stock">Stok :</label>
                        <input type="number" name="stock" id="stock" required style="width: 15rem;">
                    </div>
                    <div>
                        <label for="category_id">Kategori :</label>
                        <select name="category_id" id="category_id" style="width: 15rem; height: 40px; margin-bottom: 10px" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <label for="description">Deskripsi :</label>
                <textarea name="description" id="description" required></textarea>
                <label for="new_image">Unggah Gambar :</label>
                <input type="file" name="new_image[]" id="new_image" multiple>
                <button type="submit">Simpan</button>
            </form>
        </div>
    </main>

    <script>
        function formatRupiah(input) {
            const numberString = input.value.replace(/[^,\d]/g, "").toString();
            const split = numberString.split(",");
            const sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                const separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }
            input.value = rupiah;
        }

        // Fungsi untuk membersihkan format rupiah menjadi angka desimal sebelum dikirimkan
        function clearRupiahFormat() {
            const priceInput = document.getElementById("price");
            priceInput.value = priceInput.value.replace(/\./g, "");
        }
    </script>
    </body>
</html>