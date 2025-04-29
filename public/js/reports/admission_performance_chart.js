document.addEventListener("DOMContentLoaded", () => {
    fetchApplicantsPerAcademicYear();
});

function fetchApplicantsPerAcademicYear() {
    fetch(window.adminAdmissionPerformanceChartURL)
        .then(response => response.json())
        .then(res => {
            if (res.data) {
                renderApplicantsLineChart(res.data);
            } else {
                console.error("Invalid response:", res);
            }
        })
        .catch(err => console.error("Failed to fetch applicants per academic year:", err));
}

function renderApplicantsLineChart(data) {
    const ctx = document.getElementById('applicantsChart').getContext('2d');

    const labels = data.map(item => item.label); // Academic Year labels
    const values = data.map(item => item.total); // Applicant counts

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Applicant Growth Trend',
                    data: values,
                    borderColor: '#4070f4',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4, // Smooth curve effect
                    pointBackgroundColor: '#4070f4'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 10 }
                }
            },
            plugins: {
                legend: { display: false }, // Remove legend
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });
}
