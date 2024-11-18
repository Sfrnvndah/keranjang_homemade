document.addEventListener("DOMContentLoaded", function() {
    fetch('graphics/sales_data.json')
    .then(response => response.json())
    .then(data => {
        // Grafik
        const ctx = document.getElementById('salesChart').getContext('2d');
        let salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
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
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Ambil tahun unik dari data.labels (format tahun-bulan-hari)
        const availableYears = [...new Set(data.labels.map(label => label.substring(0, 4)))];

        // Set data awal berdasarkan tahun saat ini
        const currentYear = new Date().getFullYear().toString();
        updateChart(data, currentYear);

        // Event untuk tombol submit
        const yearPicker = document.getElementById('year-picker');
        const submitButton = document.getElementById('submit-year');
        submitButton.addEventListener('click', function() {
            const selectedYear = yearPicker.value.trim();
            if (availableYears.includes(selectedYear)) {
                updateChart(data, selectedYear);
            } else {
                showPopup("Tidak ada data dengan tahun tersebut.");
            }
        });

        // Fungsi untuk memperbarui chart
        function updateChart(data, selectedYear) {
            const filteredLabels = [];
            const filteredOnlineSales = [];
            const filteredOfflineSales = [];
            const filteredTotalSales = [];
            data.labels.forEach((label, index) => {
                if (label.startsWith(selectedYear)) { // Cocokkan awal tahun
                    filteredLabels.push(label.replace(`${selectedYear}-`, '')); // Hanya bulan
                    filteredOnlineSales.push(data.online_sales[index]);
                    filteredOfflineSales.push(data.offline_sales[index]);
                    filteredTotalSales.push(data.total_sales[index]);
                }
            });

            // Perbarui data grafik
            salesChart.data.labels = filteredLabels;

            // Filter dataset berdasarkan filter penjualan
            const filterValue = document.getElementById('sales-filter').value;
            if (filterValue === 'online') {
                salesChart.data.datasets = [
                    {
                        label: 'Online Sales',
                        data: filteredOnlineSales,
                        borderColor: 'rgb(0, 130, 127)',
                        backgroundColor: 'rgba(0, 130, 127, 0.3)',
                        fill: true,
                        tension: 0.4
                    }
                ];
            } else if (filterValue === 'offline') {
                salesChart.data.datasets = [
                    {
                        label: 'Offline Sales',
                        data: filteredOfflineSales,
                        borderColor: 'rgb(0, 130, 127)',
                        backgroundColor: 'rgba(0, 130, 127, 0.3)',
                        fill: true,
                        tension: 0.4
                    }
                ];
            } else {
                salesChart.data.datasets = [
                    {
                        label: 'Total Sales',
                        data: filteredTotalSales,
                        borderColor: 'rgb(0, 130, 127)',
                        backgroundColor: 'rgba(0, 130, 127, 0.3)',
                        fill: true,
                        tension: 0.4
                    }
                ];
            }
            salesChart.update();
        }

        function showPopup(message) {
            const popupOverlay = document.getElementById('popupOverlay');
            const popupMessage = document.getElementById('popupMessage');
            popupMessage.textContent = message;
            popupMessage.style.color = 'black'; // Paksa warna teks menjadi hitam
            popupOverlay.style.display = 'flex'; // Tampilkan pop-up
        }
        
        
        function closePopup() {
            const popupOverlay = document.getElementById('popupOverlay');
            popupOverlay.style.display = 'none'; // Sembunyikan pop-up
        }        

        // Filter Penjualan (Semua, Online, Offline)
        const filter = document.getElementById('sales-filter');
        filter.addEventListener('change', function() {
            const selectedYear = yearPicker.value || currentYear;
            updateChart(data, selectedYear);
        });
    })
    .catch(error => {
        console.error('Error loading the sales data:', error);
    });
});