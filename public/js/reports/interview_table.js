document.addEventListener("DOMContentLoaded", function () {
    const kanbanBoard = document.getElementById('interview-kanban');
    const modal = document.getElementById('interviewModal');
    const closeBtn = document.querySelector(".close");

    function fetchInterviewKanban(programs = ['all'], statuses = ['all']) {
        fetch(window.adminInterviewCalendarURL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ program_codes: programs, interview_statuses: statuses })
        })
        .then(response => response.json())
        .then(events => {
            renderKanban(events);
        })
        .catch(err => console.error("Error fetching interviews for Kanban:", err));
    }

    function renderKanban(events) {
        const grouped = {
            'Scheduled': [],
            'Completed': [],
            'Cancelled': []
        };

        events.forEach(event => {
            const status = event.extendedProps.status;
            if (grouped[status]) { // ✅ Only accept if status is Scheduled, Completed, Cancelled
                grouped[status].push(event);
            }
        });

        kanbanBoard.innerHTML = '';

        for (const [status, items] of Object.entries(grouped)) {
            const column = document.createElement('div');
            column.classList.add('kanban-column');
            column.innerHTML = `<h3>${status}</h3>`;

            items.forEach(event => {
                const item = document.createElement('div');
                item.classList.add('kanban-item');
                item.innerText = event.title;
                item.onclick = () => openInterviewModal(event);
                column.appendChild(item);
            });

            kanbanBoard.appendChild(column);
        }
    }

    function openInterviewModal(event) {
        document.getElementById("interviewModalTitle").innerText = event.title;
        document.getElementById("interviewDetails").innerHTML = `
            <p><strong>Date & Time:</strong> ${new Date(event.start).toLocaleString()}</p>
            <p><strong>Facilitator:</strong> ${event.extendedProps.user_name}</p>
            <p><strong>Applicant:</strong> ${event.extendedProps.applicant_name}</p>
            <p><strong>Program:</strong> ${event.extendedProps.program}</p>
            <p><strong>Mode:</strong> ${event.extendedProps.mode}</p>
            <p><strong>Room ID:</strong> ${event.extendedProps.room_id}</p>
            <p><strong>Status:</strong> ${event.extendedProps.status}</p>
        `;
        modal.style.display = "flex";
        document.body.classList.add("modal-open");
    }

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

    // 👉 Load initially
    fetchInterviewKanban();

    // Expose so filters can call it
    window.fetchInterviewKanban = fetchInterviewKanban;
});
