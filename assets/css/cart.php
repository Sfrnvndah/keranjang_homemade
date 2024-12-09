<?php
    session_start();
    include '../database/connection.php';
    // Ambil user_id dari session
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if (!$userId) {
        echo "<script>alert('You need to log in to view your cart!'); window.location.href='../form/login.php';</script>";
        exit;
    }
    // Query untuk mengambil data keranjang
    $query = "
        SELECT 
            c.cart_id, 
            p.product_name, 
            p.price, 
            c.quantity, 
            (p.price * c.quantity) AS total_price, 
            p.image_url 
        FROM cart c
        INNER JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = :user_id
    ";
    $item['image_url'] = 'gambar1.jpg,gambar2.jpg,gambar3.jpg';
    $images = explode(',', $item['image_url']);
    $stmt = $pdo->prepare($query); // Gunakan PDO::prepare
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT); // Bind user_id
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC); // Ambil semua data sebagai array asosiatif

    // Hitung total jika form disubmit
    $total = 0;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedCartIds = isset($_POST['selected_products']) ? $_POST['selected_products'] : [];
        if (!empty($selectedCartIds)) {
            // Buat placeholder untuk query IN
            $placeholders = implode(',', array_fill(0, count($selectedCartIds), '?'));
            $query = "
                SELECT SUM(p.price * c.quantity) AS total 
                FROM cart c
                INNER JOIN products p ON c.product_id = p.product_id
                WHERE c.cart_id IN ($placeholders)
            ";
            $stmt = $pdo->prepare($query);
            $stmt->execute($selectedCartIds);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = $result['total'] ?? 0;
        }
    }

    // Hapus produk dari keranjang
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
        $cartId = $_POST['cart_id'] ?? null;
        if ($cartId) {
            $deleteQuery = "DELETE FROM cart WHERE cart_id = :cart_id";
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->execute();
            $message = $stmt->rowCount() ? "Item berhasil dihapus dari keranjang." : "Gagal menghapus item.";
        } else {
            $message = "ID keranjang tidak ditemukan.";
        }
    }

    // Update jumlah barang di keranjang
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
        $cartId = $_POST['cart_id'] ?? null;
        $quantity = $_POST['quantity'] ?? null;

        if ($cartId && $quantity) {
            // Update jumlah di database
            $updateQuery = "UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':cart_id', $cartId, PDO::PARAM_INT);
            $stmt->execute();
        }
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
        <title>Pak Tara Craft - Shopping Cart</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../assets/css/font-awesome.css">
        <link rel="stylesheet" href="../assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="../assets/css/owl-carousel.css">
        <link rel="stylesheet" href="../assets/css/lightbox.css">
        <link rel="stylesheet" href="../assets/css/searchbar.css">
        <link rel="stylesheet" href="../assets/css/login.css">
        <link rel="stylesheet" href="../assets/css/cart.css">
        <link rel="stylesheet" href="../assets/css/popup.css">
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
                                    <a href="#" id="cart-icon">
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

        <main>
            <section class="shopping-cart">
                <div class="container">
                    <h2 class="shopping-cart-title">Keranjang</h2>
                    <table class="table custom-shopping-cart-table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Gambar</th>
                                <th>Nama</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($cartItems)): ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <?php 
                                        $images = explode(',', $item['image_url']);
                                        $firstImage = $images[0]; 
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="select-product" data-price="<?= htmlspecialchars($item['total_price']); ?>" 
                                                data-cart-id="<?= htmlspecialchars($item['cart_id']); ?>" />
                                        </td>
                                        <td>
                                            <img src="../assets/images/<?= htmlspecialchars($firstImage); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>" class="cart-product-image">
                                        </td>
                                        <td><?= htmlspecialchars($item['product_name']); ?></td>
                                        <td>
                                            <div class="quantity-container">
                                                <button type="button" class="btn btn-sm btn-secondary adjust-quantity" data-cart-id="<?= htmlspecialchars($item['cart_id']); ?>" data-action="decrease">-</button>
                                                <input type="number" class="form-control cart-quantity" value="<?= htmlspecialchars($item['quantity']); ?>" min="1" id="quantity-<?= htmlspecialchars($item['cart_id']); ?>" disabled>
                                                <button type="button" class="btn btn-sm btn-secondary adjust-quantity" data-cart-id="<?= htmlspecialchars($item['cart_id']); ?>" data-action="increase">+</button>
                                            </div>
                                        </td>
                                        <td>Rp <?= number_format($item['price'], 0, ',', '.'); ?></td>
                                        <td class="item-total">Rp <?= number_format($item['total_price'], 0, ',', '.'); ?></td>
                                        <td>
                                            <form method="post" onsubmit="return confirmRemove(this);">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['cart_id']); ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Keranjangmu Kosong!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="../index.php" class="btn-cart-cart">Lanjutkan Belanja</a>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="ml-auto mr-3">Total : <span id="total-amount" class="text-success">Rp 0</span></h4> <!-- Tambahkan margin-right -->
                                <form method="post" action="check_out.php" id="checkout-form">
                                    <input type="hidden" name="selected_products" id="selected_products">
                                    <button type="submit" class="btn-checkout">Checkout</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="popup-overlay">
                        <div class="popup-content">
                            <p></p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

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
        <script src="assets/js/custom.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Menghitung total harga saat checkbox berubah
                const checkboxes = document.querySelectorAll('.select-product');
                const totalAmountElem = document.getElementById('total-amount');
                // Fungsi untuk menghitung total harga
                function updateTotal() {
                    let total = 0;
                    checkboxes.forEach(function(checkbox) {
                        if (checkbox.checked) {
                            total += parseInt(checkbox.getAttribute('data-price'));
                        }
                    });
                    // Update tampilan total
                    totalAmountElem.textContent = `Rp ${total.toLocaleString()}`;
                }
                // Set event listener untuk checkbox
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', updateTotal);
                });
                // Initial update total on page load
                updateTotal();
            });
        </script>

        <script>
            // Konfirmasi penghapusan
            function showPopup(message, buttons, callback) {
                const popup = document.querySelector('.popup-overlay');
                const popupContent = popup.querySelector('.popup-content');
                // Clear previous popup content
                popupContent.innerHTML = `<p>${message}</p>`;
                // Tambahkan tombol sesuai parameter buttons
                buttons.forEach(button => {
                    const btn = document.createElement('button');
                    btn.textContent = button.text;
                    btn.style.marginRight = '10px';
                    btn.onclick = () => {
                        popup.style.display = 'none';
                        if (button.action) button.action(); // Menjalankan action jika ada
                        if (callback) callback();
                    };
                    popupContent.appendChild(btn);
                });
                // Tampilkan popup
                popup.style.display = 'block';
            }

            function confirmRemove(form) {
                // Menampilkan popup dengan tombol Iya dan Tidak untuk konfirmasi penghapusan
                showPopup("Apakah Anda yakin ingin menghapus item ini dari keranjang?", [
                    {
                        text: 'Iya',
                        action: () => {
                            fetch(window.location.href, {
                                method: "POST",
                                body: new FormData(form),
                            })
                            .then(response => response.text())
                            .then(() => {
                                // Menampilkan popup dengan pesan sukses setelah penghapusan
                                showPopup("Item berhasil dihapus dari keranjang.", [
                                    {
                                        text: 'OK', // Hanya tombol OK di sini
                                        action: () => window.location.reload() // Refresh halaman setelah menekan OK
                                    }
                                ]);
                            })
                            .catch(error => {
                                showPopup("Terjadi kesalahan: " + error, [
                                    {
                                        text: 'OK',
                                        action: () => {}
                                    }
                                ]);
                            });
                        }
                    },
                    {
                        text: 'Tidak',
                        action: () => {} // Menutup popup jika tombol Tidak ditekan
                    }
                ]);
                return false; // Mencegah pengiriman form secara default
            }
            // Fungsi untuk menutup popup
            function closePopup() {
                const popup = document.querySelector('.popup-overlay');
                popup.style.display = 'none';
            }
        </script>

        <script>
            // Menangani klik tombol + dan -
            document.querySelectorAll('.adjust-quantity').forEach(button => {
                button.addEventListener('click', function() {
                    const cartId = this.getAttribute('data-cart-id');
                    const action = this.getAttribute('data-action');
                    const quantityInput = document.getElementById('quantity-' + cartId);
                    let currentQuantity = parseInt(quantityInput.value);

                    if (action === 'increase') {
                        currentQuantity++;
                    } else if (action === 'decrease' && currentQuantity > 1) {
                        currentQuantity--;
                    }
                    // Update jumlah barang di input
                    quantityInput.value = currentQuantity;
                    // Kirimkan permintaan untuk update jumlah ke server
                    updateCartQuantity(cartId, currentQuantity);
                });
            });

            // Fungsi untuk mengupdate jumlah barang
            function updateCartQuantity(cartId, quantity) {
                const formData = new FormData();
                formData.append('action', 'update_quantity');
                formData.append('cart_id', cartId);
                formData.append('quantity', quantity);
                // Kirim data ke server menggunakan AJAX
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(() => {
                    // Setelah berhasil, refresh halaman untuk memperbarui total dan data lainnya
                    window.location.reload();
                })
                .catch(error => console.error('Error updating quantity:', error));
            }
        </script>

        <!-- Tombol checkout -->
        <script>
            document.querySelector('.btn-checkout').addEventListener('click', function(event) {
                event.preventDefault(); // Mencegah pengiriman form default
                const selectedProducts = [];
                const checkboxes = document.querySelectorAll('.select-product:checked');

                checkboxes.forEach(function(checkbox) {
                    const cartId = checkbox.getAttribute('data-cart-id');
                    const productName = checkbox.closest('tr').querySelector('td:nth-child(3)').textContent;
                    
                    // Ambil harga satuan dari kolom harga
                    const price = parseInt(checkbox.closest('tr').querySelector('td:nth-child(5)').textContent.replace('Rp ', '').replace(/\./g, '').trim());
                    
                    // Ambil jumlah dari input quantity
                    const quantity = parseInt(checkbox.closest('tr').querySelector('.cart-quantity').value);
                    
                    // Ambil subtotal dari kolom subtotal
                    const subtotal = parseInt(checkbox.closest('tr').querySelector('.item-total').textContent.replace('Rp ', '').replace(/\./g, '').trim());

                    selectedProducts.push({
                        cart_id: cartId,
                        product_name: productName,
                        price: price, // Harga satuan
                        quantity: quantity,
                        total_price: subtotal, // Subtotal
                        image_url: checkbox.closest('tr').querySelector('img').src.split('/').pop() // Ambil nama gambar
                    });
                });

                // Kirim data ke input hidden
                document.getElementById('selected_products').value = JSON.stringify(selectedProducts);
                document.getElementById('checkout-form').submit(); // Kirim form
            });
        </script>
    </body>
</html>