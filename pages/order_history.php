<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "keranjang_homemade";

// Mengaktifkan laporan kesalahan
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Menambah riwayat pesanan
if (isset($_POST['tambah_pesanan'])) {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $produk = $_POST['produk'];
    $jumlah = $_POST['jumlah'];
    
    $sql = "INSERT INTO order_history (nama_pelanggan, produk, jumlah, status, jumlah_dibayar, status_pembayaran, metode_pembayaran) 
            VALUES ('$nama_pelanggan', '$produk', $jumlah, 'Pending', 0, 'Belum Lunas', NULL)";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Pesanan berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Mengubah status riwayat pesanan
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE order_history SET status='$status' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Status pesanan berhasil diubah!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Memproses pembayaran
if (isset($_POST['tambah_pembayaran'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $jumlah_dibayar = $_POST['jumlah_dibayar'];

    $sqlPesanan = "SELECT * FROM order_history WHERE id = $id_pesanan";
    $resultPesanan = $conn->query($sqlPesanan);

    if ($resultPesanan->num_rows > 0) {
        $rowPesanan = $resultPesanan->fetch_assoc();
        $total_dibayar = $rowPesanan['jumlah_dibayar'] + $jumlah_dibayar;

        $total_harga = $rowPesanan['jumlah'] * 10000;
        $status_pembayaran = ($total_dibayar >= $total_harga) ? 'Lunas' : 'Belum Lunas';

        $sqlUpdate = "UPDATE order_history 
                      SET metode_pembayaran = '$metode_pembayaran', 
                          jumlah_dibayar = $total_dibayar, 
                          status_pembayaran = '$status_pembayaran' 
                      WHERE id = $id_pesanan";

        if ($conn->query($sqlUpdate) === TRUE) {
            echo "<script>alert('Pembayaran berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Pesanan tidak ditemukan!');</script>";
    }
}

// Mencari riwayat pesanan
$cari_pesanan = '';
if (isset($_POST['cari'])) {
    $cari_pesanan = $_POST['cari_pesanan'];
}

// Menampilkan semua riwayat pesanan
$sqlPesanan = "SELECT * FROM order_history WHERE nama_pelanggan LIKE '%$cari_pesanan%' OR id LIKE '%$cari_pesanan%'";
$resultPesanan = $conn->query($sqlPesanan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Riwayat Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            margin-top: 50px;
        }
        h2, h3 {
            color: #800000 ; /* Ubah warna header menjadi #A97C8B */
        }
        .btn-custom {
            background-color: #800000 ; /* Ubah warna tombol */
            color: white;
            border: none;
        }
        .btn-custom:hover {
            background-color: #8c5d65; /* Warna tombol saat hover */
        }
        table {
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            text-align: left;
            padding: 12px;
        }
        th {
            background-color: #800000 ; /* Ubah warna background header tabel */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .form-control {
            border-radius: 8px;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-control, .btn-custom, table {
            border-color: #800000 ; /* Warna border untuk input, tombol, dan tabel */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Kelola Riwayat Pesanan</h2>
    
    <div class="form-section">
        <h3>Tambah Pesanan</h3>
        <form method="post" class="row">
            <div class="col-md-4">
                <input type="text" name="nama_pelanggan" class="form-control" placeholder="Nama Pelanggan" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="produk" class="form-control" placeholder="Nama Produk" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required min="1">
            </div>
            <div class="col-md-2">
                <button type="submit" name="tambah_pesanan" class="btn btn-custom">Tambah Pesanan</button>
            </div>
        </form>
    </div>

    <div class="form-section">
        <h3>Cari Riwayat Pesanan</h3>
        <form method="post" class="d-flex">
            <input type="text" name="cari_pesanan" class="form-control" placeholder="Cari berdasarkan nama atau ID">
            <button type="submit" name="cari" class="btn btn-custom ms-2">Cari</button>
        </form>
    </div>

    <div class="form-section">
        <h3>Tambah Pembayaran</h3>
        <form method="post" class="row">
            <div class="col-md-3">
                <input type="number" name="id_pesanan" class="form-control" placeholder="Masukkan ID Pesanan" required>
            </div>
            <div class="col-md-3">
                <select name="metode_pembayaran" class="form-control" required>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="Cash">Cash</option>
                    <option value="Kartu Kredit">Kartu Kredit</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="jumlah_dibayar" class="form-control" placeholder="Jumlah Dibayar" required step="0.01">
            </div>
            <div class="col-md-3">
                <button type="submit" name="tambah_pembayaran" class="btn btn-custom">Tambah Pembayaran</button>
            </div>
        </form>
    </div>

    <div class="form-section">
        <h3>Daftar Riwayat Pesanan</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Status Pembayaran</th>
                    <th>Jumlah Dibayar</th>
                    <th>Metode Pembayaran</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultPesanan->num_rows > 0): ?>
                    <?php while($rowPesanan = $resultPesanan->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rowPesanan['id']); ?></td>
                            <td><?php echo htmlspecialchars($rowPesanan['nama_pelanggan']); ?></td>
                            <td><?php echo htmlspecialchars($rowPesanan['produk']); ?></td>
                            <td><?php echo htmlspecialchars($rowPesanan['jumlah']); ?></td>
                            <td><?php echo htmlspecialchars($rowPesanan['status']); ?></td>
                            <td><?php echo htmlspecialchars($rowPesanan['status_pembayaran']); ?></td>
                            <td><?php echo number_format($rowPesanan['jumlah_dibayar'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($rowPesanan['metode_pembayaran']); ?></td>
                            <td><?php echo htmlspecialchars($rowPesanan['tanggal']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9">Tidak ada riwayat pesanan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$conn->close();
?>
