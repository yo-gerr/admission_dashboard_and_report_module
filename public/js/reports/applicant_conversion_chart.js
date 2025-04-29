document.addEventListener("DOMContentLoaded", () => {
    fetchApplicantConversionData();
});

function fetchApplicantConversionData() {
    const academicYearIds = [...document.querySelectorAll("#filter-academic-year .item.checked")]
        .map(item => item.dataset.value);

    fetch(window.adminApplicantConversionChartURL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ academic_year_ids: academicYearIds })
    })
        .then(response => response.json())
        .then(data => {
            if (data.total_applicants !== undefined) {
                renderApplicantConversionChart(data);
            } else {
                console.error("Invalid response:", data);
            }
        })
        .catch(error => console.error("Error fetching applicant conversion data:", error));
}

let applicantConversionChartInstance = null; // global variable to hold chart instance

function renderApplicantConversionChart(data) {
    const ctx = document.getElementById('applicantConversionChart').getContext('2d');

    // 🔥 Destroy existing chart instance if it exists
    if (applicantConversionChartInstance) {
        applicantConversionChartInstance.destroy();
    }

    const funnelData = {
        labels: ["Total Applicants", "Admitted Applicants", "Enrolled Applicants"],
        datasets: [{
            data: [data.total_applicants, data.admitted_applicants, data.enrolled_applicants],
            backgroundColor: ["rgba(54, 162, 235, 0.7)", "rgba(255, 206, 86, 0.7)", "rgba(75, 192, 192, 0.7)"],
            borderColor: ["rgba(54, 162, 235, 1)", "rgba(255, 206, 86, 1)", "rgba(75, 192, 192, 1)"],
            borderWidth: 1
        }]
    };

    // ✅ Assign to global variable
    applicantConversionChartInstance = new Chart(ctx, {
        type: 'bar',
        data: funnelData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true
                },
                y: {
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}
