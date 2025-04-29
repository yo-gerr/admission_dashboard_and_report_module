document.addEventListener("DOMContentLoaded", () => {
    fetchProgramCapacityDemand();
});

function fetchProgramCapacityDemand() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error("CSRF token not found!");
        return;
    }

    const academicYearIds = [...document.querySelectorAll("#filter-academic-year .checked")]
        .map(item => item.dataset.value);

    const programCodes = [...document.querySelectorAll("#filter-program-code .checked")]
        .map(item => item.dataset.value);

    fetch(window.adminProgramCapacityDemandChartURL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken.content
        },
        body: JSON.stringify({ academic_year_ids: academicYearIds, program_codes: programCodes })
    })
    .then(response => response.json())
    .then(res => {
        if (res.data) {
            renderProgramCapacityDemandChart(res.data);
        } else {
            console.error("Invalid response:", res);
        }
    })
    .catch(err => console.error("Failed to fetch program capacity vs demand:", err));
}

let programCapacityDemandChartInstance = null;

function renderProgramCapacityDemandChart(data) {
    const ctx = document.getElementById('programCapacityDemandChart').getContext('2d');

    // Destroy old chart if exists
    if (programCapacityDemandChartInstance !== null) {
        programCapacityDemandChartInstance.destroy();
    }

    const labels = data.map(item => item.label);
    const capacities = data.map(item => item.capacity);
    const demands = data.map(item => item.demand);

    programCapacityDemandChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Capacity',
                    data: capacities,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Demand',
                    data: demands,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: { size: 12 }
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
