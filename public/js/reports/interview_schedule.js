document.addEventListener("DOMContentLoaded", function () {
    let calendarEl = document.getElementById("calendar");
    let modal = document.getElementById("interviewModal");
    let closeBtn = document.querySelector(".close");

    function fetchInterviewSchedules(selectedPrograms = ['all'], selectedStatuses = ['all']) {
        fetch(window.adminInterviewCalendarURL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                program_codes: selectedPrograms,
                interview_statuses: selectedStatuses
            })
        })
        .then(response => response.json())
        .then(events => {
            calendar.removeAllEvents();
            calendar.addEventSource(events);
        })
        .catch(err => console.error("Error fetching interview schedules:", err));
    }    

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "timeGridWeek",
        height: "350px",
        contentHeight: "auto",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,listWeek"
        },
        events: [],
        eventDidMount: function (info) {
            let eventStatus = info.event.extendedProps.status;
            info.el.style.backgroundColor =
                eventStatus === "Completed" ? "#28a745" :
                eventStatus === "Cancelled" ? "#dc3545" :
                eventStatus === "Scheduled" ? "#ffc107" :
                "#6c757d"; // Default Gray
        },
        eventClick: function (info) {
            document.getElementById("interviewModalTitle").innerText = info.event.title;
            document.getElementById("interviewDetails").innerHTML = `
                <p><strong>Date & Time:</strong> ${info.event.start.toLocaleString()}</p>
                <p><strong>Facilitator:</strong> ${info.event.extendedProps.user_name}</p>
                <p><strong>Applicant:</strong> ${info.event.extendedProps.applicant_name}</p>
                <p><strong>Program:</strong> ${info.event.extendedProps.program}</p>
                <p><strong>Mode:</strong> ${info.event.extendedProps.mode}</p>
                <p><strong>Room ID:</strong> ${info.event.extendedProps.room_id}</p>
                <p><strong>Status:</strong> ${info.event.extendedProps.status}</p>
            `;

            modal.style.display = "flex";
            document.body.classList.add("modal-open");
        }
    });

    calendar.render();
    fetchInterviewSchedules(); // Load all programs initially

    closeBtn.onclick = function () {
        modal.style.display = "none";
        document.body.classList.remove("modal-open");
    };

    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
            document.body.classList.remove("modal-open");
        }
    };

    window.fetchInterviewSchedules = fetchInterviewSchedules; // Expose function globally
});