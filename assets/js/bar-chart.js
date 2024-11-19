document.addEventListener("DOMContentLoaded", function () {
    fetch('graphics/top_products.json')
        .then(response => response.json())
        .then(topProductsData => {
            // Ambil elemen HTML
            const yearPicker = document.getElementById('year-picker');
            const submitButton = document.getElementById('submit-year');
            const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');

            // Inisialisasi grafik produk terlaris
            const topProductsBarChart = new Chart(topProductsCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Penjualan Online',
                            data: [],
                            backgroundColor: 'rgba(255, 183, 77, 0.7)',
                            borderColor: 'rgb(255, 183, 77)',
                            borderWidth: 1
                        },
                        {
                            label: 'Penjualan Offline',
                            data: [],
                            backgroundColor: 'rgba(138, 138, 138, 0.7)',
                            borderColor: 'rgb(138, 138, 138)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Nama Produk'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Jumlah Produk Terjual'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });

            // Fungsi untuk memperbarui data chart
            function updateChart(year) {
                if (topProductsData.data[year]) {
                    const yearData = topProductsData.data[year];
                    topProductsBarChart.data.labels = yearData.products;
                    topProductsBarChart.data.datasets[0].data = yearData.online_sales_quantities;
                    topProductsBarChart.data.datasets[1].data = yearData.offline_sales_quantities;
                    topProductsBarChart.update();
                } else {}
            }

            // Event listener untuk tombol submit
            submitButton.addEventListener('click', function () {
                const selectedYear = yearPicker.value;
                if (topProductsData.years.includes(parseInt(selectedYear))) {
                    updateChart(selectedYear);
                } else {}
            });

            // Tampilkan data awal (tahun terbaru)
            const initialYear = topProductsData.years[0];
            yearPicker.value = initialYear;
            updateChart(initialYear);
        })
        .catch(error => {
            console.error('Error loading the top products data:', error);
        });
})