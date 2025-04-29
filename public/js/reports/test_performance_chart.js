document.addEventListener("DOMContentLoaded", () => {
    fetchAdmissionTestPerformanceData();
});

function fetchAdmissionTestPerformanceData() {
    const academicYearIds = [...document.querySelectorAll("#filter-academic-year .checked")]
        .map(item => item.dataset.value);

    fetch(window.adminTestPerformanceChartURL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ academic_year_ids: academicYearIds })
    })
        .then(response => response.json())
        .then(data => {
            if (data.passed_applicants !== undefined && data.failed_applicants !== undefined) {
                renderAdmissionTestPerformanceChart(data);
            } else {
                console.error("Invalid response:", data);
            }
        })
        .catch(error => console.error("Error fetching admission test performance data:", error));
}

let testPerformanceChartInstance = null;

function renderAdmissionTestPerformanceChart(data) {
    const ctx = document.getElementById('testPerformanceChart').getContext('2d');

    // 🔸 Destroy existing chart instance if it exists
    if (testPerformanceChartInstance !== null) {
        testPerformanceChartInstance.destroy();
    }

    // Define data for test performance tracking
    const performanceData = {
        labels: ["Passed", "Failed"],
        datasets: [{
            label: "Test Performance",
            data: [data.passed_applicants, data.failed_applicants],
            backgroundColor: ["rgba(75, 192, 192, 0.7)", "rgba(255, 99, 132, 0.7)"],
            borderColor: ["rgba(75, 192, 192, 1)", "rgba(255, 99, 132, 1)"],
            borderWidth: 1
        }]
    };

    // Create and assign new chart instance
    testPerformanceChartInstance = new Chart(ctx, {
        type: 'pie',
        data: performanceData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    enabled: true
                },
                datalabels: {
                    color: "#000",
                    font: {
                        size: 14,
                        weight: "bold"
                    },
                    formatter: (value, context) => {
                        return context.chart.data.labels[context.dataIndex] + ": " + value;
                    }
                }
            }
        }
    });
}
