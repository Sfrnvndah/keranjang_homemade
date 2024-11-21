<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta - Keranjang Homemade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        header {
            background-color: #00796b;
            color: white;
            padding: 10px 20px;
            text-align: center;
        }
        main {
            padding: 20px;
        }
        section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #00796b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .button {
            display: inline-block;
            padding: 8px 15px;
            color: white;
            background-color: #00796b;
            border: none;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #005b4f;
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
                <li class="active">
                    <a href="admin.php">
                        <img src="../assets/images/icon-dashboard.png" alt="Dashboard" width="24px" height="24px">
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_produk.php">
                        <img src="../assets/images/icon-product.png" alt="Dashboard" width="24px" height="24px">
                        <span>Produk</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_pesanan.php">
                        <img src="../assets/images/icon-order.png" alt="Dashboard" width="24px" height="24px">
                        <span>Pesanan</span>
                    </a>
                </li>
                <li>
                    <a href="admin/daftar_pembayaran.php">
                        <img src="../assets/images/icon-payment.png" alt="Dashboard" width="24px" height="24px">
                        <span>Daftar Pembayaran</span>
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
                        <li><a href="report_sales.php">Penjualan</a></li>
                        <li><a href="report_finance.php">Keunagan</a></li>
                        <li><a href="report_payment.php">Pembayaran</a></li>
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
                        <li><a href="sales_recording.php">Penjualan Offline</a></li>
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
                        <!-- <li><a href="offline_training.php">Daftar Pelatihan Offline</a></li> -->
                        <li><a href="training_history.php">Riwayat Pendaftar</a></li>
                    </div>
                    </ul>
                </li>
                <li>
                    <a href="admin/profile.php">
                        <img src="../assets/images/icon-profile.png" alt="Dashboard" width="24px" height="24px">
                        <span>Profile</span>
                    </a>
                </li>
                <!-- <div class="logout-container">
                    <button class="logout-btn" onclick="window.location.href='logout.php'">
                        <span>Logout</span>
                    </button>
                </div> -->
            </ul>
        </nav>
    <main>
        <section>
            <h2>Tambahkan Peserta Baru</h2>
            <form id="formPeserta" onsubmit="tambahPeserta(event)">
                <label for="namaPeserta">Nama:</label>
                <input type="text" id="namaPeserta" placeholder="Masukkan nama peserta" required>
                
                <label for="kontakPeserta">Kontak:</label>
                <input type="text" id="kontakPeserta" placeholder="Masukkan nomor kontak" required>
                
                <label for="statusPembayaran">Status Pembayaran:</label>
                <select id="statusPembayaran" required>
                    <option value="">Pilih Status</option>
                    <option value="Lunas">Lunas</option>
                    <option value="Belum Lunas">Belum Lunas</option>
                </select>
                
                <button type="submit" class="button">Tambah Peserta</button>
            </form>
        </section>

        <section>
            <h2>Daftar Peserta</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Lokasi Pelatihan</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody id="tabelPeserta">
                    <!-- Baris peserta akan muncul di sini -->
                </tbody>
            </table>
        </section>
    </main>

    <script>
        function tambahPeserta(event) {
            event.preventDefault();

            // Ambil nilai dari input form
            const nama = document.getElementById('namaPeserta').value.trim();
            const kontak = document.getElementById('kontakPeserta').value.trim();
            const status = document.getElementById('statusPembayaran').value;

            // Validasi form
            if (!nama || !kontak || !status) {
                alert("Harap isi semua kolom!");
                return;
            }

            // Ambil elemen tbody tabel
            const tabelPeserta = document.getElementById('tabelPeserta');

            // Buat baris baru
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${nama}</td>
                <td>${kontak}</td>
                <td>Lokasi Pelatihan Belum Ditentukan</td>
                <td>${status}</td>
            `;
            tabelPeserta.appendChild(newRow);
            // Kosongkan input setelah menambahkan data
            document.getElementById('namaPeserta').value = '';
            document.getElementById('kontakPeserta').value = '';
            document.getElementById('statusPembayaran').value = '';
        }
    </script>
</body>
</html>