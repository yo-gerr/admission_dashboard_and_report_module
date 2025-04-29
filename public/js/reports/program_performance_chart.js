document.addEventListener("DOMContentLoaded", () => {
    fetchProgramPerformance(); // Fetch all programs with no filter
});

function fetchProgramPerformance(programCodes = []) {
    fetch(window.adminProgramPerformanceChartURL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ program_codes: programCodes.length ? programCodes : ['all'] })
    })
    .then(response => response.json())
    .then(res => {
        if (res.data && typeof res.data === 'object') {
            const formattedData = Object.entries(res.data).map(([programCode, details]) => ({
                label: programCode,
                trend: details.trend
            }));
            renderApplicantsLineChart(formattedData);
        } else {
            clearChart();
        }
    })
    .catch(err => console.error("Error fetching performance chart:", err));
}

let programChartInstance = null;

function renderApplicantsLineChart(data) {
    const ctx = document.getElementById('programPerformanceChart').getContext('2d');
    if (programChartInstance) programChartInstance.destroy();

    const labels = [...new Set(data.flatMap(p => p.trend.map(y => y.year)))];
    const datasets = data.map(program => {
        const color = getRandomColor();
        return {
            label: program.label,
            data: labels.map(year => {
                const y = program.trend.find(t => t.year === year);
                return y ? y.applicants : 0;
            }),
            borderColor: color,
            backgroundColor: color,
            fill: false,
            tension: 0.3,
            pointBackgroundColor: color
        };
    });

    programChartInstance = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: false,
                    text: "Applicant Growth Trends",
                    font: { size: 18 }
                }
            }
        }
    });
}

function clearChart() {
    const ctx = document.getElementById('programPerformanceChart').getContext('2d');
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
    console.log("No data available for all programs");
}

// Generates random colors for each program
function getRandomColor() {
    return `rgb(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)})`;
}
