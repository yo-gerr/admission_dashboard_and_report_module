document.addEventListener("DOMContentLoaded", () => {
    const cardBodies = document.querySelectorAll(".card-body");

    const currentView = document.body.dataset.view || "unknown";

    cardBodies.forEach(cardBody => {
        const filterType = cardBody.dataset.filterType;
        const selectBtn = cardBody.querySelector(".select-btn");
        if (!selectBtn) return;

        const btnText = selectBtn.querySelector(".btn-text");
        const items = cardBody.querySelectorAll(".item");
        const allItem = [...items].find(item => item.querySelector(".item-text").innerText === "All");

        // Toggle dropdown on button click
        selectBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const isOpen = selectBtn.classList.contains("open");

            closeAllDropdowns(); // Close others first

            if (!isOpen) {
                selectBtn.classList.add("open");
                selectBtn.closest(".card")?.classList.add("dropdown-open");
                masonryLayout(); // 🧩 Recalculate layout & z-index
            }
        });

        // Handle item selection
        items.forEach(item => {
            item.addEventListener("click", (e) => {
                e.stopPropagation();
                const isAll = item === allItem;
                const isChecked = item.classList.toggle("checked");

                if (isAll) {
                    items.forEach(i => isChecked ? i.classList.add("checked") : i.classList.remove("checked"));
                } else {
                    allItem.classList.remove("checked");
                }

                updateDropdownText(cardBody);

                if (currentView !== "interviewer_dashboard") {
                    fetchFilteredApplicants();

                    if (filterType === 'academic-year') {
                        fetchApplicantConversionData();
                        fetchProgramCapacityDemand();
                        fetchAdmissionTestPerformanceData();
                        fetchAdmittedNonEnrolledData();
                    }
                    if (filterType === 'program-code') {
                        fetchProgramCapacityDemand();

                        const selectedPrograms = [...document.querySelectorAll("#filter-program-code .checked")]
                            .map(item => item.dataset.value);

                        if (currentView === "program_coordinator_dashboard") {
                            if (selectedPrograms.length === 0 || selectedPrograms.includes("all")) {
                                fetchProgramPerformance(); // all
                            } else {
                                fetchProgramPerformance(selectedPrograms); // filtered
                            }

                            window.fetchInterviewSchedules(selectedPrograms.length ? selectedPrograms : ['all']);
                        }
                    }
                }

                if (currentView === "interviewer_dashboard") {
                    fetchTotalInterviews();

                    if (filterType === 'program-code' || filterType === 'interview-status') {
                        const selectedPrograms = [...document.querySelectorAll("#filter-program-code .checked")]
                            .map(item => item.dataset.value);
                        const selectedStatuses = [...document.querySelectorAll("#filter-interview-status .checked")]
                            .map(item => item.dataset.value);

                        const programs = (selectedPrograms.length === 0 || selectedPrograms.includes("all")) ? ['all'] : selectedPrograms;
                        const statuses = (selectedStatuses.length === 0 || selectedStatuses.includes("all")) ? ['all'] : selectedStatuses;

                        // Refresh interview calendar with both filters
                        window.fetchInterviewSchedules(programs, statuses);
                    }
                }
            });
        });

        updateDropdownText(cardBody);
    });

    function updateDropdownText(cardBody) {
        const btnText = cardBody.querySelector(".btn-text");
        if (!btnText) return;

        const selectedCount = cardBody.querySelectorAll(".checked:not(:first-child)").length;
        btnText.innerText = selectedCount > 0 ? `${selectedCount} Selected` : "All";
    }

    function closeAllDropdowns() {
        document.querySelectorAll(".select-btn.open").forEach(openBtn => {
            openBtn.classList.remove("open");
            openBtn.closest(".card")?.classList.remove("dropdown-open");
        });
        masonryLayout(); // 🔄 Always recalculate layout on close too
    }

    document.addEventListener("click", () => closeAllDropdowns());

    if (currentView !== "interviewer_dashboard") {
        setInterval(fetchFilteredApplicants, 10000);
    }
    if (currentView === "interviewer_dashboard") {
        setInterval(fetchTotalInterviews, 10000);
    }
});

/* 🔹 Fetch total applicants based on selected filters */
function fetchFilteredApplicants() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error("CSRF token not found!");
        return;
    }

    const academicYearIds = [...document.querySelectorAll("#filter-academic-year .checked")]
        .map(item => item.dataset.value);

    const statusOptions = [...document.querySelectorAll("#filter-status .checked")]
        .map(item => item.dataset.value);

    const programCodes = [...document.querySelectorAll("#filter-program-code .checked")]
        .map(item => item.dataset.value);

    fetch(window.adminTotalApplicantsURL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken.getAttribute("content")
        },
        body: JSON.stringify({ academic_year_ids: academicYearIds, status_options: statusOptions, program_codes: programCodes })
    })
        .then(response => response.json())
        .then(data => {
            const totalApplicantsCount = document.querySelector("#total-applicants .total-applicants-count .count");
            if (totalApplicantsCount) {
                totalApplicantsCount.innerText = new Intl.NumberFormat().format(data.total);
            } else {
                console.error("Total applicants count element not found.");
            }
        })
        .catch(error => console.error("Error fetching applicants:", error));
}

/* 🔹 Fetch total interviews based on selected filters */
function fetchTotalInterviews() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error("CSRF token not found!");
        return;
    }

    const selectedPrograms = [...document.querySelectorAll("#filter-program-code .checked")]
        .map(item => item.dataset.value);

    const selectedStatuses = [...document.querySelectorAll("#filter-interview-status .checked")]
        .map(item => item.dataset.value);

    const programs = (selectedPrograms.length === 0 || selectedPrograms.includes("all")) ? ['all'] : selectedPrograms;
    const statuses = (selectedStatuses.length === 0 || selectedStatuses.includes("all")) ? ['all'] : selectedStatuses;

    fetch(window.adminTotalInterviewURL, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken.getAttribute("content")
        },
        body: JSON.stringify({ program_codes: programs, interview_statuses: statuses })
    })
    .then(response => response.json())
    .then(data => {
        const totalInterviewCount = document.querySelector("#total-interview .total-interview-count .count");
        if (totalInterviewCount) {
            totalInterviewCount.innerText = new Intl.NumberFormat().format(data.total_interviews);
        } else {
            console.error("Total interview count element not found.");
        }
    })
    .catch(error => console.error("Error fetching total interview count:", error));
}
