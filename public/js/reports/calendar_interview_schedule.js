// Hardcoded interview schedules
const interviewSchedules = [
    { date: '2023-04-10', name: 'John Doe', time: '10:00 AM', status: 'Scheduled' },
    { date: '2023-04-11', name: 'Jane Smith', time: '2:00 PM', status: 'Scheduled' },
    { date: '2023-04-12', name: 'Emily Johnson', time: '9:30 AM', status: 'Pending' }
];

// Initialize calendar
const calendarGrid = document.getElementById('calendarGrid');
const calendarMonthYear = document.getElementById('calendarMonthYear');
let currentDate = new Date();

function renderCalendar(date) {
    // Clear current calendar grid
    calendarGrid.innerHTML = '';

    // Get month details
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Set header
    calendarMonthYear.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;

    // Add empty blocks for days before the first day of the month
    for (let i = 0; i < firstDay; i++) {
        const emptyBlock = document.createElement('div');
        emptyBlock.className = 'calendar-day';
        calendarGrid.appendChild(emptyBlock);
    }

    // Populate calendar days
    for (let day = 1; day <= daysInMonth; day++) {
        const calendarDay = document.createElement('div');
        calendarDay.className = 'calendar-day';
        calendarDay.textContent = day;

        // Check for scheduled interviews
        const currentDateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const schedule = interviewSchedules.find(s => s.date === currentDateString);

        if (schedule) {
            calendarDay.classList.add('scheduled');
            const popup = document.createElement('div');
            popup.className = 'schedule-popup';
            popup.innerHTML = `
                <strong>${schedule.name}</strong><br>
                ${schedule.time}<br>
                Status: ${schedule.status}
            `;
            calendarDay.appendChild(popup);
        }

        calendarGrid.appendChild(calendarDay);
    }
}

// Month navigation
document.getElementById('prevMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
});

document.getElementById('nextMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
});

// Initial render
renderCalendar(currentDate);