<?php
    require '../vendor/autoload.php';
    use Dompdf\Dompdf;
    include('../database/connection.php');
    $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
    try {
        $sql = "
            SELECT 
                ROW_NUMBER() OVER (ORDER BY combined_data.order_date) AS No,
                combined_data.order_date AS Tanggal,
                combined_data.source AS Source,
                combined_data.total_pendapatan AS TotalPendapatan
            FROM (
                SELECT 
                    o.order_date,
                    'Online' AS source,
                    o.total_amount AS total_pendapatan
                FROM orders o
                WHERE o.status = 'completed' AND YEAR(o.order_date) = :year
                
                UNION ALL
                
                SELECT 
                    oo.order_date,
                    'Offline' AS source,
                    oo.total_amount AS total_pendapatan
                FROM offline_orders oo
                WHERE oo.status = 'completed' AND YEAR(oo.order_date) = :year
            ) combined_data
            ORDER BY combined_data.order_date;
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':year', $selectedYear, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalPendapatan = 0;
        foreach ($data as $row) {
            $totalPendapatan += $row['TotalPendapatan'];
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
    <h2 style='text-align: center;'>Laporan Pendapatan Tahun $selectedYear</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Source</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>";
        if (!empty($data)) {
            foreach ($data as $row) {
                $html .= "<tr>
                    <td>{$row['No']}</td>
                    <td>{$row['Tanggal']}</td>
                    <td>{$row['Source']}</td>
                    <td>Rp " . number_format($row['TotalPendapatan'], 0, ',', '.') . "</td>
                </tr>";
            }
            $html .= "<tr>
                <td colspan='3' style='text-align: right; font-weight: bold;'>Total Keseluruhan</td>
                <td>Rp " . number_format($totalPendapatan, 0, ',', '.') . "</td>
            </tr>";
        } else {
            $html .= "<tr><td colspan='4'>Tidak ada data</td></tr>";
        }
        $html .= "</tbody></table>";

        // Proses PDF dengan Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Laporan_Pendapatan_Tahun_$selectedYear.pdf", ["Attachment" => true]);
    exit;
?>
