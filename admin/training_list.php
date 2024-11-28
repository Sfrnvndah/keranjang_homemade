<?php
    require '../database/connection.php';
    // Menambah Data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tanggal = $_POST['tanggal'] ?? null;
        $jam = $_POST['jam'] ?? null;
        $lokasi = $_POST['lokasi'] ?? null;
        $biaya = $_POST['biaya'] ?? null;
        if (!$tanggal || !$jam || !$lokasi || !$biaya) {
            $error = "Semua kolom wajib diisi.";
        } else {
            // Format data
            $tanggal = date('Y-m-d', strtotime($tanggal));
            $jam = $jam . ':00';
            $biaya = floatval(str_replace('.', '', $biaya));
            try {
                $stmt = $pdo->prepare("INSERT INTO training_list (date, time, location, price) VALUES (:tanggal, :jam, :lokasi, :biaya)");
                $stmt->bindParam(':tanggal', $tanggal);
                $stmt->bindParam(':jam', $jam);
                $stmt->bindParam(':lokasi', $lokasi);
                $stmt->bindParam(':biaya', $biaya);
                if ($stmt->execute()) {
                    $success = "Pelatihan berhasil ditambahkan.";
                } else {
                    $error = "Gagal menyimpan data.";
                }
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }

    // Menghapus data
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_training'])) {
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM training_list WHERE training_list_id = :training_list_id");
                $stmt->execute([':training_list_id' => $id]);
                // Redirect ke halaman daftar pelatihan dengan pesan sukses
                header("Location: training_list.php?message=success");
                exit;
            } catch (PDOException $e) {
                // Redirect ke halaman daftar pelatihan dengan pesan error
                header("Location: training_list.php?message=error");
                exit;
            }
        }
    }

    // Menampilkan data di tabel
    $trainings = [];
    try {
        $stmt = $pdo->query("SELECT * FROM training_list ORDER BY date, time");
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Pelatihan</title>
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
                        <li><a href="../report/report_sales.php">Penjualan</a></li>
                        <li><a href="../report/report_finance.php">Keuangan</a></li>
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
                        <li><a href="#">Daftar Pelatihan</a></li>
                        <li><a href="participants_list.php">Daftar Peserta</a></li>
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
                <h2>Tambahkan Pelatihan Baru</h2>
                <form method="POST" action="training_list.php">
                    <input type="hidden" name="add_training" value="1">
                    <div class="form-row">
                        <div>
                            <label for="tanggalPelatihan">Tanggal :</label>
                            <input type="date" name="tanggal" required>
                        </div>
                        <div>
                            <label for="jamPelatihan">Jam :</label>
                            <input type="time" name="jam" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div>
                            <label for="lokasiPelatihan">Lokasi Pelatihan :</label>
                            <input type="text" name="lokasi" placeholder="Masukkan lokasi pelatihan" required>
                        </div>
                        <div>
                            <label for="biayaPelatihan">Biaya :</label>
                            <input type="text" name="biaya" placeholder="Masukkan biaya" required>
                        </div>
                    </div>
                    <button type="submit" class="button">Tambah Pelatihan</button>
                </form>
            </section>

            <div class="popup-overlay">
                <div class="popup-content"></div>
            </div>

            <section>
                <h2>Daftar Pelatihan</h2>
                <?php if (isset($successMessage)) : ?>
                    <p style="color: green;"><?= $successMessage ?></p>
                <?php elseif (isset($errorMessage)) : ?>
                    <p style="color: red;"><?= $errorMessage ?></p>
                <?php endif; ?>
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center;">Tanggal</th>
                            <th style="text-align: center;">Jam</th>
                            <th style="text-align: center;">Lokasi</th>
                            <th style="text-align: center;">Biaya</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trainings as $training) : ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($training['date'])) ?></td>
                                <td><?= $training['time'] ?></td>
                                <td><?= htmlspecialchars($training['location']) ?></td>
                                <td>Rp <?= number_format($training['price'], 0, ',', '.') ?></td>
                                <td style="text-align: center;">
                                    <form method="POST" action="training_list.php" onsubmit="return confirmRemove(this);">
                                        <input type="hidden" name="delete_training" value="1">
                                        <input type="hidden" name="id" value="<?= $training['training_list_id'] ?>">
                                        <button class="delete-button" style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;" data-id="<?= $training['training_list_id'] ?>">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>

        <?php if (!empty($error)): ?>
            <div style="color: red;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div style="color: green;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['message'])): ?>
            <p style="color: <?= $_GET['message'] === 'success' ? 'green' : 'red'; ?>">
                <?= $_GET['message'] === 'success' ? 'Pelatihan berhasil dihapus.' : 'Terjadi kesalahan saat menghapus data.'; ?>
            </p>
        <?php endif; ?>
    </body>
</html>