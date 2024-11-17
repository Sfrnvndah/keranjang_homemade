document.addEventListener("DOMContentLoaded", function() {
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
                        borderColor: 'rgb(0, 130, 127)',
                        backgroundColor: 'rgba(0, 130, 127, 0.3)',
                        fill: true, // Fill grafik
                        tension: 0.4 // Efek lengkung/gelombang
                    },
                    {
                        label: 'Offline Sales',
                        data: data.offline_sales,
                        borderColor: 'rgb(0, 130, 127)',
                        backgroundColor: 'rgba(0, 130, 127, 0.3)',
                        fill: true, // Fill grafik
                        tension: 0.4 // Efek lengkung/gelombang
                    }
                ]
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
                    // Memasukkan data tahun ke dropdown
                    const yearSelect = document.getElementById('year-filter');
                    data.years.forEach(year => {
                        const option = document.createElement('option');
                        option.value = year;
                        option.textContent = year;
                        yearSelect.appendChild(option);
                    });
                    // Set tahun awal ke dropdown dan update grafik
                    yearSelect.value = data.years[0]; // Set tahun pertama sebagai default
                    updateChart(data, yearSelect.value); // Update chart dengan data tahun pertama
                    // Update grafik berdasarkan tahun yang dipilih
                    yearSelect.addEventListener('change', function(event) {
                        const selectedYear = event.target.value;
                        updateChart(data, selectedYear);
                    });
                    // Filter dan update chart berdasarkan tahun yang dipilih
                    function updateChart(data, selectedYear) {
                        const filteredLabels = [];
                        const filteredOnlineSales = [];
                        const filteredOfflineSales = [];
                        const filteredTotalSales = [];
                        data.labels.forEach((label, index) => {
                            if (label.includes(selectedYear)) {
                                filteredLabels.push(label.replace(`${selectedYear}-`, ''));
                                filteredOnlineSales.push(data.online_sales[index]);
                                filteredOfflineSales.push(data.offline_sales[index]);
                                filteredTotalSales.push(data.total_sales[index]);
                            }
                        });
                        // Update data grafik
                        salesChart.data.labels = filteredLabels;
                        // Update dataset sesuai dengan pilihan filter
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
                    // Filter Penjualan (Semua, Online, Offline)
                    const filter = document.getElementById('sales-filter');
                    filter.addEventListener('change', function(event) {
                        const selectedYear = yearSelect.value;
                        updateChart(data, selectedYear); // Update chart berdasarkan pilihan filter
                    });
                })
                .catch(error => {
                    console.error('Error loading the sales data:', error);
                });
            }); 