<?php
    include('../database/connection.php');

    // Proses penghapusan data
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_participant'])) {
        $contact = $_POST['contact'];
        $jenis = $_POST['jenis'];
        if ($jenis === 'Offline') {
            $stmt = $pdo->prepare("DELETE FROM offline_training_participants WHERE contact = :contact");
        } else {
            $stmt = $pdo->prepare("DELETE FROM online_training_participants WHERE user_id = (
                SELECT user_id FROM users WHERE phone = :contact LIMIT 1
            )");
        }
        $stmt->execute(['contact' => $contact]);
    }

    // Query untuk mengambil semua tanggal dari tabel "training_list"
    $stmt = $pdo->query("SELECT DISTINCT date FROM training_list ORDER BY date");
    $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $selectedDate = isset($_GET['date']) ? $_GET['date'] : '';

    // Mengambil harga dari database
    if ($selectedDate) {
        $stmt = $pdo->prepare("SELECT price FROM training_list WHERE date = :date LIMIT 1");
        $stmt->execute(['date' => $selectedDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $price = $result['price'];
            $formattedPrice = "Rp " . number_format($price, 0, ',', '.');
        } else {
            $formattedPrice = '';
        }
    } else {
        $formattedPrice = '';
    }

    // Mengambil harga dari database
    if ($selectedDate) {
        $stmt = $pdo->prepare("SELECT location FROM training_list WHERE date = :date LIMIT 1");
        $stmt->execute(['date' => $selectedDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $location = $result['location'];
        } else {
            $location = '';
        }
    } else {
        $location = '';
    }

    // Proses penyimpanan data
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_participants'])) {
        $name = $_POST['namapeserta'];
        $contact = $_POST['kontakpeserta'];
        $selectedDate = $_POST['tanggal'];
        // Ambil training_list_id berdasarkan tanggal yang dipilih
        $stmt = $pdo->prepare("SELECT training_list_id FROM training_list WHERE date = :date LIMIT 1");
        $stmt->execute(['date' => $selectedDate]);
        $training = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($training) {
            $training_list_id = $training['training_list_id'];
            // Masukkan data ke dalam tabel offline_training_participants
            $stmt = $pdo->prepare("INSERT INTO offline_training_participants (training_list_id, name, contact) VALUES (:training_list_id, :name, :contact)");
            $stmt->execute([
                'training_list_id' => $training_list_id,
                'name' => $name,
                'contact' => $contact,
            ]);
        }
    }

    // Query untuk mengambil semua tanggal dari tabel "training_list"
    $stmt = $pdo->query("SELECT DISTINCT date FROM training_list ORDER BY date");
    $dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $selectedDate = isset($_GET['date']) ? $_GET['date'] : '';

    // Query untuk menampilkan daftar peserta
    $stmt = $pdo->query("
        SELECT p.name, p.contact, t.date AS training_date
        FROM offline_training_participants p
        JOIN training_list t ON p.training_list_id = t.training_list_id
        ORDER BY t.date, p.name
    ");
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil data dari tabel offline_training_participants
    $stmtoffline = $pdo->query("
    SELECT 
        p.name, 
        p.contact, 
        t.date AS training_date
    FROM offline_training_participants p
    JOIN training_list t ON p.training_list_id = t.training_list_id
    ORDER BY t.date, p.name
    ");
    $offlineParticipants = $stmtoffline->fetchAll(PDO::FETCH_ASSOC);

    // Ambil data dari tabel online_training_participants
    $stmtonline = $pdo->query("
    SELECT 
        u.name AS participant_name, 
        u.phone AS contact, 
        t.date AS training_date
    FROM online_training_participants p
    JOIN users u ON p.user_id = u.user_id
    JOIN training_list t ON p.training_list_id = t.training_list_id
    ORDER BY t.date, u.name
    ");
    $onlineParticipants = $stmtonline->fetchAll(PDO::FETCH_ASSOC);

    // Gabungkan data dari kedua tabel
    $allParticipants = array_merge(
        array_map(function($participant) {
            $participant['jenis'] = 'Offline';
            return $participant;
        }, $offlineParticipants),
        array_map(function($participant) {
            $participant['jenis'] = 'Online';
            return $participant;
        }, $onlineParticipants)
    );
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="../assets/css/sidebar.css">
        <script type="text/javascript" src="../assets/js/sidebar.js" defer></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Peserta</title>
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
                <h2>Tambahkan Peserta Baru</h2>
                <form method="POST" action="participants_list.php">
                    <input type="hidden" name="add_participants" value="1">
                    <div class="form-row">
                        <div>
                            <label for="tanggal">Tanggal :</label>
                            <select name="tanggal" id="tanggal" class="dropdown-tanggal" required>
                                <option value="">Pilih Tanggal</option>
                                <?php
                                foreach ($dates as $date) {
                                    $formattedDate = date("Y-m-d", strtotime($date['date']));
                                    $selected = ($formattedDate == $selectedDate) ? 'selected' : '';
                                    echo "<option value='$formattedDate' $selected>$formattedDate</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="biayaPelatihan">Biaya :</label>
                            <input type="text" name="biaya" id="biaya" placeholder="Biaya" value="<?php echo $formattedPrice; ?>" readonly required>
                        </div>
                        <div>
                            <label for="lokasiPelatihan">Lokasi :</label>
                            <input type="text" name="lokasi" id="lokasi" placeholder="Lokasi" value="<?php echo $location; ?>" readonly required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div>
                            <label for="namaPeserta">Nama Peserta :</label>
                            <input type="text" name="namapeserta" placeholder="Masukkan nama peserta" required>
                        </div>
                        <div>
                            <label for="kontakPeserta">Kontak :</label>
                            <input type="text" name="kontakpeserta" placeholder="Masukkan nomor telepon" required>
                        </div>
                    </div>
                    <button type="submit" class="button">Tambah Peserta</button>
                </form>
            </section>

            <section>
                <h2>Daftar Peserta</h2>
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center;">Nama</th>
                            <th style="text-align: center;">Kontak</th>
                            <th style="text-align: center;">Tanggal Pelatihan</th>
                            <th style="text-align: center;">Metode Pendaftaran</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelPeserta">
                        <?php foreach ($allParticipants as $participant): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($participant['name'] ?? $participant['participant_name']); ?></td>
                                <td><?php echo htmlspecialchars($participant['contact']); ?></td>
                                <td><?php echo htmlspecialchars(date("Y-m-d", strtotime($participant['training_date']))); ?></td>
                                <td><?php echo htmlspecialchars($participant['jenis']); ?></td>
                                <td style="text-align: center;">
                                    <form method="POST" action="participants_list.php">
                                        <input type="hidden" name="delete_participant" value="1">
                                        <input type="hidden" name="contact" value="<?php echo htmlspecialchars($participant['contact']); ?>">
                                        <input type="hidden" name="jenis" value="<?php echo htmlspecialchars($participant['jenis']); ?>">
                                        <button type="submit" class="delete-button" style="background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </main>

        <script>
            const tanggalDropdown = document.getElementById('tanggal');
            const biayaInput = document.getElementById('biaya');
            tanggalDropdown.addEventListener('change', function() {
                const selectedDate = this.value;
                if (selectedDate) {
                    window.location.href = "participants_list.php?date=" + encodeURIComponent(selectedDate);
                }
            });
        </script>
    </body>
</html>