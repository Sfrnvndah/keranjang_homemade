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
        try {
            $customerName = $_POST['namacustomer'] ?? '';
            $orderDate = $_POST['date'] ?? '';
            $paymentMethod = $_POST['stok'] ?? '';
            $totalAmount = intval(preg_replace('/[^0-9]/', '', $_POST['total_amount'] ?? '0'));
            if (empty($customerName) || empty($orderDate) || empty($paymentMethod) || $totalAmount <= 0) {
                throw new Exception("Harap isi semua data dengan benar.");
            }
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("
                INSERT INTO offline_orders (customer_name, total_amount, status, order_date, payment_method)
                VALUES (:customer_name, :total_amount, 'Completed', :order_date, :payment_method)
            ");
            $stmt->execute([
                ':customer_name' => $customerName,
                ':total_amount' => $totalAmount,
                ':order_date' => $orderDate,
                ':payment_method' => $paymentMethod,
            ]);
            $offlineOrderId = $pdo->lastInsertId();
            if (isset($_POST['products']) && is_array($_POST['products'])) {
                $stmt = $pdo->prepare("
                    INSERT INTO offline_order_items (offline_order_id, product_id, quantity)
                    VALUES (:offline_order_id, :product_id, :quantity)
                ");
                foreach ($_POST['products'] as $product) {
                    $stmt->execute([
                        ':offline_order_id' => $offlineOrderId,
                        ':product_id' => $product['product_id'],
                        ':quantity' => $product['quantity'],
                    ]);
                }
            }
            $pdo->commit();
            echo "<script>showPopup('Pesanan berhasil disimpan!');</script>";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<script>showPopup('Terjadi kesalahan: {$e->getMessage()}');</script>";
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
        <title>Penjualan Offline</title>
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
        <link rel="stylesheet" href="../assets/css/participants-list.css">
        <link rel="stylesheet" href="../assets/css/logout.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="icon" type="image/png" href="../assets/images/logo.png">
        <style>
            .action-column {
                text-align: center;
                vertical-align: middle;
            }
            .delete-button {
                background-color: #ff4d4d;
                color: white;
                border: none;
                padding: 5px 10px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: background-color 0.3s ease;
            }
            .delete-button:hover {
                background-color: #cc0000;
            }
        </style>
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
                        <li><a href="#">Daftar Peserta</a></li>
                    </div>
                    </ul>
                </li>
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
                <h2>Penjualan Offline</h2>
                <form id="order-form" onsubmit="addProduct(event)">
                    <div class="form-row">
                        <div>
                            <label>Nama Customer</label>
                            <input type="text" name="namacustomer" id="namacustomer" placeholder="Nama Customer">
                        </div>
                        <div>
                            <label>Tanggal Penjualan :</label>
                            <input type="date" id="date" name="date" style="height: 40px;">
                        </div>
                        <div>
                            <label for="stok">Metode Pembayaran :</label>
                            <input type="text" name="stok">
                        </div>
                    </div>
                    <div class="form-row">
                        <div>
                            <label for="produk">Nama Produk :</label>
                            <input type="text" name="produk" id="produk" placeholder="Nama Produk" onkeyup="fetchProductData()">
                        </div>
                        <div>
                            <label for="jumlah">Jumlah :</label>
                            <input type="text" name="jumlah">
                        </div>
                        <div>
                            <label for="stok">Stok :</label>
                            <input type="text" name="stok" id="stok" readonly>
                        </div>
                        <div>
                            <label for="harga">Harga :</label>
                            <input type="text" name="harga" id="harga" readonly>
                        </div>
                    </div>
                    <button type="submit" class="button">Tambah Barang</button>
                </form>
            </section>

            <section>
                <h2>Daftar Produk</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center;">Nama Barang</th>
                            <th style="text-align: center;">Jumlah</th>
                            <th style="text-align: center;">Harga</th>
                            <th style="text-align: center;">Subtotal</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: bold;">Total :</td>
                            <td id="total-amount"">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="button" id="submit-order" style="margin-top: 10px;">Submit</button>
                
                <!-- Popup -->
                <div class="popup-overlay" id="popup">
                    <div class="popup-content">
                        <p id="popup-message" style="color: black;"></p>
                        <button onclick="closePopup()">Tutup</button>
                    </div>
                </div>
            </section>
        </main>
        
        <script>
            // Mengisi stok secara otomatis
            function fetchProductData() {
                var productName = document.getElementById('produk').value;
                if (productName.length > 0) {
                    $.ajax({
                        url: 'fetch_product.php',
                        type: 'POST',
                        data: { product_name: productName },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.success) {
                                document.getElementById('stok').value = data.stock;
                            } else {
                                document.getElementById('stok').value = '';
                            }
                        }
                    });
                } else {
                    document.getElementById('stok').value = '';
                }
            }
            // Mengisi harga secara otomatis
            function fetchProductData() {
                var productName = document.getElementById('produk').value;
                if (productName.length > 0) {
                    $.ajax({
                        url: 'fetch_product.php',
                        type: 'POST',
                        data: { product_name: productName },
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.success) {
                                document.getElementById('stok').value = data.stock; // Isi kolom stok
                                document.getElementById('harga').value = formatRupiah(data.price); // Format dan isi kolom harga
                            } else {
                                document.getElementById('stok').value = ''; // Kosongkan jika tidak ditemukan
                                document.getElementById('harga').value = ''; // Kosongkan jika tidak ditemukan
                            }
                        }
                    });
                } else {
                    document.getElementById('stok').value = ''; // Kosongkan jika input kosong
                    document.getElementById('harga').value = ''; // Kosongkan jika input kosong
                }
            }
        </script>

        <!-- Format harga -->
        <script>
            function formatRupiah(angka) {
                var number_string = angka.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return 'Rp ' + rupiah;
            }
        </script>

        <script>
            let total = 0;
            function addProduct(event) {
                event.preventDefault();
                const productName = document.getElementById('produk').value;
                const quantity = parseInt(document.querySelector('[name="jumlah"]').value);
                const stock = parseInt(document.getElementById('stok').value);
                const price = parseInt(document.getElementById('harga').value.replace(/[^0-9]/g, ''));
                if (!productName || isNaN(quantity) || isNaN(stock) || isNaN(price)) {
                    showPopup('Harap isi semua kolom dengan benar!');
                    return;
                }
                if (quantity > stock) {
                    showPopup('Jumlah yang dimasukkan melebihi stok yang tersedia.');
                    return;
                }
                // Menghitung subtotal
                const subtotal = quantity * price;
                // Menambahkan subtotal ke total
                total += subtotal;
                updateTotalDisplay();
                // Menambahkan data ke tabel
                const tbody = document.querySelector('table tbody');
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${productName}</td>
                    <td>${quantity}</td>
                    <td>${formatRupiah(price)}</td>
                    <td>${formatRupiah(subtotal)}</td>
                    <td class="action-column"><button class="delete-button" onclick="deleteRow(this, ${subtotal})">Hapus</button></td>
                `;
                tbody.appendChild(newRow);
                document.getElementById('order-form').reset();
                document.getElementById('stok').value = '';
                document.getElementById('harga').value = '';
            }
            function deleteRow(button, subtotal) {
                // Kurangi subtotal dari total
                total -= subtotal;
                updateTotalDisplay();
                // Hapus baris dari tabel
                const row = button.parentElement.parentElement;
                row.remove();
            }
            function updateTotalDisplay() {
                // Menampilkan total ke elemen <tfoot>
                document.getElementById('total-amount').textContent = formatRupiah(total);
            }
            // Format rupiah
            function formatRupiah(angka) {
                var number_string = angka.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/g);
                if (ribuan) {
                    var separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return 'Rp ' + rupiah;
            }
        </script>

        <script>
            document.getElementById('submit-order').addEventListener('click', function () {
                const customerName = document.getElementById('namacustomer').value;
                const orderDate = document.getElementById('date').value;
                const paymentMethod = document.querySelector('[name="stok"]').value;
                const totalAmount = parseInt(document.getElementById('total-amount').textContent.replace(/[^0-9]/g, ''));
                const orderItems = [];
                const rows = document.querySelectorAll('table tbody tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    orderItems.push({
                        productName: cells[0].textContent,
                        quantity: parseInt(cells[1].textContent),
                        price: parseInt(cells[2].textContent.replace(/[^0-9]/g, ''))
                    });
                });
                if (!customerName || !orderDate || !paymentMethod || orderItems.length === 0) {
                    showPopup('Harap lengkapi semua data sebelum submit!');
                    return;
                }
                // Mengirim data ke server
                fetch('submit_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `customer_name=${encodeURIComponent(customerName)}&order_date=${orderDate}&payment_method=${encodeURIComponent(paymentMethod)}&total_amount=${totalAmount}&order_items=${JSON.stringify(orderItems)}`
                })
                .then(response => response.text())
                .then(result => {
                    if (result === 'success') {
                        showPopup('Order berhasil disimpan!');
                        window.location.reload();
                    } else {
                        showPopup('Terjadi kesalahan saat menyimpan order.');
                        console.error(result);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        </script>

        <script>
            function showPopup(message) {
                document.getElementById('popup-message').textContent = message;
                document.getElementById('popup').style.display = 'flex';
            }
            function closePopup() {
                document.getElementById('popup').style.display = 'none';
            }
        </script>
    </body>
</html>