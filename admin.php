<?php
    $notebookPath = 'graphics/python.ipynb';
    $command = 'jupyter nbconvert --to notebook --execute ' . escapeshellarg($notebookPath);
    $output = shell_exec($command);
    echo $output;
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
        <title>Pak Tara Craft - Admin Dashboard</title>
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
        <link rel="stylesheet" href="assets/css/templatemo-hexashop.css">
        <link rel="stylesheet" href="assets/css/owl-carousel.css">
        <link rel="stylesheet" href="assets/css/lightbox.css">
        <link rel="stylesheet" href="assets/css/admin-dashboard.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    </head>

    <body>
        <header class="header-area header-sticky">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="main-nav">
                            <a href="index.php" class="logo">
                                <img src="assets/images/logo.png" alt="Pak Tara Craft Logo">
                            </a>
                            <ul class="nav">
                                <li><a href="admin.php" class="active">Dashboard</a></li>
                                <li><a href="admin/product_lists.php">Daftar Produk</a></li>
                                <li class="submenu">
                                    <a href="javascript:;">Penjualan</a>
                                    <ul>
                                        <li><a href="admin/offline.php">Penjualan Offline</a></li>
                                        <li><a href="admin/order.php">Pesanan</a></li>
                                        <li><a href="admin/payment.php">Pembayaran</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:;">Laporan</a>
                                    <ul>
                                        <li><a href="admin/report_money.php">Keuangan</a></li>
                                        <li><a href="admin/report_product.php">Produk Terlaris</a></li>
                                        <li><a href="admin/report_payment.php">Pembayaran</a></li>
                                        <li><a href="admin/report_sales.php">Penjualan</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:;">Pelatihan</a>
                                    <ul>
                                        <li><a href="admin/program_lists.php">Daftar Program</a></li>
                                        <li><a href="admin/participants_lists.php">Daftar Peserta</a></li>
                                        <li><a href="admin/schedule.php">Jadwal</a></li>
                                        <li><a href="admin/training_history.php">Riwayat pendaftaran</a></li>
                                        <li><a href="admin/training_report.php">Laporan Pelatihan</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <section class="section" id="admin-dashboard">
            <div class="container">
                <div class="row">
                    <!-- Grafik Penjualan -->
                    <div class="col-lg-8">
                        <div class="dashboard-card">
                            <h4>Grafik Penjualan Tahun 2024</h4>
                            <input id="year-picker" type="text" placeholder="Pilih Tahun" />
                            <select id="sales-filter">
                                <option value="all">Semua Penjualan</option>
                                <option value="online">Penjualan Online</option>
                                <option value="offline">Penjualan Offline</option>
                            </select>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>

                    <!-- Statistik Pesanan dan Pendapatan -->                    
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="dashboard-card">
                                    <h4>Statistik Pesanan</h4>
                                    <p>Total Pesanan: <span id="order-count">123</span></p>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="dashboard-card">
                                    <h4>Statistik Pendapatan</h4>
                                    <p>Total Pendapatan: Rp<span id="revenue">5,000,000</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pesanan Baru dan Notifikasi Update -->
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="dashboard-card">
                                    <h4>Pesanan Baru</h4>
                                    <ul class="order-list">
                                        <li>Order #1234 - Rp500,000</li>
                                        <li>Order #1235 - Rp1,200,000</li>
                                        <li>Order #1236 - Rp350,000</li>
                                        <li><a href="../admin/orders.php">Lihat semua pesanan</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="dashboard-card">
                                    <h4>Notifikasi Update</h4>
                                    <ul class="notification-list">
                                        <li>Order #1231 telah dikirim</li>
                                        <li>Pengembalian untuk Order #1229 diproses</li>
                                        <li>Order #1232 dibatalkan</li>
                                        <li><a href="../admin/notifications.php">Lihat semua notifikasi</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Tahun
                flatpickr("#year-picker", {
                    dateFormat: "Y",
                    onChange: function(selectedDates, dateStr, instance) {
                        const year = dateStr;
                        console.log('Tahun terpilih:', year);
                        updateChartForYear(year);
                    }
                });
                
                fetch('graphics/sales_data.json')
                .then(response => response.json())
                .then(data => {
                    // Grafik
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    let salesChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [
                                {
                                    label: 'Online Sales',
                                    data: data.online_sales,
                                    borderColor: 'rgb(75, 192, 192)',
                                    fill: false
                                },
                                {
                                    label: 'Offline Sales',
                                    data: data.offline_sales,
                                    borderColor: 'rgb(255, 99, 132)',
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Month'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Sales Amount'
                                    }
                                }
                            }
                        }
                    });
                    // Filter Penjualan
                    const filter = document.getElementById('sales-filter');
                    filter.addEventListener('change', function(event) {
                        const value = event.target.value;
                        if (value === 'online') {
                            salesChart.data.datasets = [{
                                label: 'Online Sales',
                                data: data.online_sales,
                                borderColor: 'rgb(75, 192, 192)',
                                fill: false
                            }];
                        } else if (value === 'offline') {
                            salesChart.data.datasets = [{
                                label: 'Offline Sales',
                                data: data.offline_sales,
                                borderColor: 'rgb(255, 99, 132)',
                                fill: false
                            }];
                        } else {
                            salesChart.data.datasets = [
                                {
                                    label: 'Online Sales',
                                    data: data.online_sales,
                                    borderColor: 'rgb(75, 192, 192)',
                                    fill: false
                                },
                                {
                                    label: 'Offline Sales',
                                    data: data.offline_sales,
                                    borderColor: 'rgb(255, 99, 132)',
                                    fill: false
                                }
                            ];
                        }
                        salesChart.update();
                    });
                    // Tahun
                    function updateChartForYear(year) {
                        const filteredData = filterDataByYear(year, data);
                        if (filteredData.labels.length === 0) {
                            // Menampilkan pesan jika tidak ada data untuk tahun yang dipilih
                            alert('Tidak ada data untuk tahun ' + year); // Kamu bisa mengganti ini dengan cara lain, seperti menampilkan teks di halaman
                        } else {
                            salesChart.data.labels = filteredData.labels;
                            salesChart.data.datasets[0].data = filteredData.online_sales;
                            salesChart.data.datasets[1].data = filteredData.offline_sales;
                            salesChart.update();
                        }
                    }
                    // Tahun
                    function filterDataByYear(year, data) {
                        let filteredLabels = [];
                        let filteredOnlineSales = [];
                        let filteredOfflineSales = [];
                        for (let i = 0; i < data.labels.length; i++) {
                            let month = data.labels[i];
                            if (month.includes(year)) {
                                filteredLabels.push(month);
                                filteredOnlineSales.push(data.online_sales[i]);
                                filteredOfflineSales.push(data.offline_sales[i]);
                            }
                        }
                        return {
                            labels: filteredLabels,
                            online_sales: filteredOnlineSales,
                            offline_sales: filteredOfflineSales
                        };
                    }
                })
                .catch(error => {
                    console.error('Error loading the sales data:', error);
                });
            });
        </script>
    </body>
</html>
