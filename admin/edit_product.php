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

    // Ambil data produk berdasarkan ID
    $product_id = $_GET['id'] ?? '';
    if (!$product_id) {
        die('ID produk tidak valid!');
    }
    
    try {
        // Query untuk mendapatkan data produk berdasarkan ID
        $query = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            die('Produk tidak ditemukan!');
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
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

    // Cek apakah ada permintaan untuk menghapus gambar
    if (isset($_GET['delete_image'])) {
        $image_to_delete = $_GET['delete_image'];
        $existing_images = $product['image_url'];

        // Menghapus gambar yang dipilih dari daftar gambar
        $images = explode(",", $existing_images);
        $updated_images = array_filter($images, function($image) use ($image_to_delete) {
            return trim($image) !== $image_to_delete;
        });

        // Mengupdate daftar gambar di database
        $updated_images_str = implode(",", $updated_images);
        
        try {
            $query = "UPDATE products SET image_url = :image_url WHERE product_id = :product_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':image_url', $updated_images_str, PDO::PARAM_STR);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();

            // Menghapus file gambar dari server
            $image_path = "../assets/images/" . $image_to_delete;
            if (file_exists($image_path)) {
                unlink($image_path); // Menghapus file gambar
            }

            // Redirect untuk menghindari pengiriman ulang form
            header("Location: edit_product.php?id=" . $product_id);
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            die();
        }
    }

    // Jika form di-submit (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $category_id = $_POST['category_id'];
        $existing_images = $product['image_url'];

        // Memeriksa apakah ada gambar baru yang diunggah
        if (!empty($_FILES['new_image']['name'][0])) {
            $uploaded_images = [];
            foreach ($_FILES['new_image']['tmp_name'] as $key => $tmp_name) {
                $file_name = basename($_FILES['new_image']['name'][$key]);
                $target_path = "../assets/images/" . $file_name;
                if (move_uploaded_file($tmp_name, $target_path)) {
                    $uploaded_images[] = $file_name;
                }
            }
            // Tambahkan gambar baru ke daftar gambar yang sudah ada
            if (!empty($uploaded_images)) {
                $existing_images .= ',' . implode(',', $uploaded_images);
            }
        }

        // Update data produk
        try {
            $query = "UPDATE products 
                    SET product_name = :product_name, 
                        description = :description, 
                        price = :price, 
                        stock = :stock, 
                        category_id = :category_id, 
                        image_url = :image_url
                    WHERE product_id = :product_id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_INT);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':image_url', $existing_images, PDO::PARAM_STR);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
            echo "Produk berhasil diperbarui!";
            $_SESSION['message'] = "Produk berhasil diperbarui!";
            // Redirect ke halaman product.php
            header("Location: product.php");
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            die();
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Produk</title>
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
                <h1 class="edit-product-header" style="margin-top: 20px; margin-bottom: 60px">Edit Produk</h1>
                <form method="POST" enctype="multipart/form-data" onsubmit="clearRupiahFormat()">
                    <div style="display: flex; gap: 2rem; align-items: center;">
                        <div>
                            <label for="product_name">Nama Produk :</label>
                            <input type="text" name="product_name" id="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" style="width: 15rem;" required>
                        </div>
                        <div>
                            <label for="price">Harga :</label>
                            <input 
                                type="text" 
                                name="price" 
                                id="price" 
                                value="<?= number_format($product['price'], 0, ',', '.') ?>" 
                                style="width: 15rem;" 
                                required 
                                oninput="formatRupiah(this)">
                        </div>
                        <div>
                            <label for="stock">Stok :</label>
                            <input type="number" name="stock" id="stock" value="<?= htmlspecialchars($product['stock']) ?>" style="width: 15rem;" required>
                        </div>
                        <div>
                            <label for="category_id">Kategori :</label>
                            <select name="category_id" id="category_id" style="width: 15rem; height: 40px; margin-bottom: 10px" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['category_id']) ?>"
                                        <?= $category['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <label for="description">Deskripsi :</label>
                    <textarea name="description" id="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                    <label>Gambar Produk :</label>
                    <?php
                        $images = explode(",", $product['image_url']);
                        foreach ($images as $image) {
                            echo "<div style='display: flex; gap: 1rem; align-items: center;'>";
                            echo "<img src='../assets/images/" . htmlspecialchars(trim($image)) . "' alt='Gambar Produk' width='100'>";
                            echo "<button type='button' onclick='showDeletePopup(\"" . htmlspecialchars(trim($image)) . "\")'>Hapus</button>";
                            echo "</div>";
                        }
                    ?>
                    <label for="new_image">Unggah Gambar :</label>
                    <input type="file" name="new_image[]" id="new_image" multiple>
                    <button type="submit">Simpan Perubahan</button>
                </form>
            </div>
        </main>

        <!-- Popup Konfirmasi -->
        <div class="popup-overlay" id="popup-overlay" style="display: none;">
            <div class="popup-content">
                <p style="color: black; font-size: 16px; text-align: center;">Apakah Anda yakin ingin menghapus gambar ini?</p>
                <button id="confirm-delete" style="margin-top: 10px; padding: 5px 10px; background-color: red; color: white; border: none; border-radius: 5px; cursor: pointer;">Ya</button>
                <button id="cancel-delete" style="margin-top: 10px; padding: 5px 10px; background-color: gray; color: white; border: none; border-radius: 5px; cursor: pointer;">Tidak</button>
            </div>
        </div>

        <script>
            // Fungsi untuk memformat angka menjadi format "30.000"
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

        <script>
            // Menyimpan ID gambar yang akan dihapus
            let imageToDelete = '';
            // Menampilkan popup konfirmasi
            function showDeletePopup(imageName) {
                imageToDelete = imageName; // Menyimpan nama gambar yang akan dihapus
                document.getElementById('popup-overlay').style.display = 'block'; // Menampilkan popup
            }
            // Konfirmasi penghapusan gambar
            document.getElementById('confirm-delete').addEventListener('click', function() {
                window.location.href = "?id=" + <?= $product_id ?> + "&delete_image=" + imageToDelete; // Arahkan ke server untuk menghapus gambar
            });
            // Batal penghapusan gambar
            document.getElementById('cancel-delete').addEventListener('click', function() {
                closeDeletePopup(); // Menutup popup jika batal
            });
        </script>
    </body>
</html>