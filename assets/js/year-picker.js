    document.addEventListener("DOMContentLoaded", function () {
        flatpickr("#year-picker", {
            dateFormat: "Y", // Format hanya tahun
            defaultDate: new Date(), // Tahun saat ini sebagai default
            plugins: [new flatpickr.plugins.yearSelect({ // Aktifkan mode pemilihan tahun
                minDate: "2000", // Tahun minimum
                maxDate: "2100"  // Tahun maksimum
            })]
        });
    });
