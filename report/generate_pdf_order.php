<?php
    require '../vendor/autoload.php';
    use Dompdf\Dompdf;
    include('../database/connection.php');

    $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

    try {
        $sql = "
            SELECT 
                orders.order_date AS Tanggal,
                users.username AS Pelanggan,
                GROUP_CONCAT(products.product_name SEPARATOR ', ') AS Produk,
                'Transfer' AS Metode_Pembayaran,
                SUM(order_items.quantity) AS Jumlah_Beli,
                MIN(products.price) AS Harga,
                SUM(order_items.quantity * products.price) AS Total
            FROM orders
            JOIN order_items ON orders.order_id = order_items.order_id
            JOIN products ON order_items.product_id = products.product_id
            JOIN users ON orders.user_id = users.user_id
            WHERE orders.status = 'completed' AND YEAR(orders.order_date) = :year
            GROUP BY orders.order_date, users.username

            UNION ALL

            SELECT
                offline_orders.order_date AS Tanggal,
                offline_orders.customer_name AS Pelanggan,
                GROUP_CONCAT(products.product_name SEPARATOR ', ') AS Produk,
                offline_orders.payment_method AS Metode_Pembayaran,
                SUM(offline_order_items.quantity) AS Jumlah_Beli,
                MIN(products.price) AS Harga,
                SUM(offline_order_items.quantity * products.price) AS Total
            FROM offline_orders
            JOIN offline_order_items ON offline_orders.offline_order_id = offline_order_items.offline_order_id
            JOIN products ON offline_order_items.product_id = products.product_id
            WHERE offline_orders.status = 'completed' AND YEAR(offline_orders.order_date) = :year
            GROUP BY offline_orders.order_date, offline_orders.customer_name, offline_orders.payment_method;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalHarga = 0;
        $totalPendapatan = 0;
        foreach ($data as $row) {
            $totalHarga += $row['Harga'];
            $totalPendapatan += $row['Total'];
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    // HTML untuk PDF
    $html = "
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <h2 style='text-align: center;'>Laporan Penjualan Tahun $selectedYear</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Produk</th>
                <th>Metode Pembayaran</th>
                <th>Jumlah Beli</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>";

    if (!empty($data)) {
        $no = 1;
        foreach ($data as $row) {
            $html .= "<tr>
                <td>{$no}</td>
                <td>{$row['Tanggal']}</td>
                <td>{$row['Pelanggan']}</td>
                <td>{$row['Produk']}</td>
                <td>{$row['Metode_Pembayaran']}</td>
                <td>{$row['Jumlah_Beli']}</td>
                <td>Rp " . number_format($row['Harga'], 0, ',', '.') . "</td>
                <td>Rp " . number_format($row['Total'], 0, ',', '.') . "</td>
            </tr>";
            $no++;
        }
        $html .= "<tr>
            <td colspan='6' style='text-align: right; font-weight: bold;'>Total Keseluruhan</td>
            <td>Rp " . number_format($totalHarga, 0, ',', '.') . "</td>
            <td>Rp " . number_format($totalPendapatan, 0, ',', '.') . "</td>
        </tr>";
    } else {
        $html .= "<tr><td colspan='8'>Tidak ada data</td></tr>";
    }

    $html .= "</tbody></table>";

    // Proses PDF dengan Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("Laporan_Penjualan_Tahun_$selectedYear.pdf", ["Attachment" => true]);
    exit;
?>