document.addEventListener("DOMContentLoaded", () => {
    fetchAdmittedNonEnrolledData();
});

function fetchAdmittedNonEnrolledData() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error("CSRF token not found!");
        return;
    }

    // Get selected academic years from the filter
    const academicYearIds = [...document.querySelectorAll("#filter-academic-year .checked")]
        .map(item => item.dataset.value);

    fetch(window.adminAdmittedNonEnrolledChartURL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken.content
        },
        body: JSON.stringify({ academic_year_ids: academicYearIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.admitted_applicants !== undefined) {
            renderAdmittedNonEnrolledChart(data);
        } else {
            console.error("Invalid response:", data);
        }
    })
    .catch(error => console.error("Error fetching admitted non-enrolled data:", error));
}

let admittedNonEnrolledChartInstance = null;

function renderAdmittedNonEnrolledChart(data) {
    const ctx = document.getElementById('admittedButNotEnrolledChart').getContext('2d');

    // Destroy the previous chart instance if it exists
    if (admittedNonEnrolledChartInstance !== null) {
        admittedNonEnrolledChartInstance.destroy();
    }

    // Define data for the multi-line chart
    const analysisData = {
        labels: ["Admitted", "Enrolled", "Not Enrolled"], // Labels remain single-line
        datasets: [
            {
                label: "Admitted",
                data: [data.admitted_applicants, data.admitted_applicants, data.admitted_applicants],
                borderColor: "rgba(54, 162, 235, 1)",
                backgroundColor: "rgba(54, 162, 235, 0.2)",
                borderWidth: 2,
                fill: false
            },
            {
                label: "Enrolled",
                data: [0, data.enrolled_applicants, data.enrolled_applicants],
                borderColor: "rgba(75, 192, 192, 1)",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                borderWidth: 2,
                fill: false
            },
            {
                label: "Not Enrolled",
                data: [0, 0, data.non_enrolled_applicants],
                borderColor: "rgba(255, 99, 132, 1)",
                backgroundColor: "rgba(255, 99, 132, 0.2)",
                borderWidth: 2,
                fill: false
            }
        ]
    };

    // Create a new chart instance
    admittedNonEnrolledChartInstance = new Chart(ctx, {
        type: 'line',
        data: analysisData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    enabled: true
                },
                legend: {
                    display: true,
                    position: "top",
                    labels: {
                        boxWidth: 15, // Reduce legend box size for compact look
                        font: {
                            size: 12 // Adjust font size for single-line labels
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: false // Hide x-axis labels (removes "Stages" label)
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: false // Remove "Applicants" label
                    }
                }
            }
        }
    });
}
