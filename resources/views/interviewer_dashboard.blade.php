<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Facilitator Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/sidebar_template/sidebar_template.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reports/reports.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="{{ asset('js/reports/masonry_layout.js') }}" defer></script>
    <script src="{{ asset('js/reports/filter.js') }}" defer></script>'
    <script src="{{ asset('js/reports/interview_schedule.js') }}" defer></script>
    <script src="{{ asset('js/reports/interview_table.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body data-view="interviewer_dashboard">
    @include('sidebar_template') <!-- Sidebar -->

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard">
            <div id="filter-program-code" class="card small-card filter-card">
                <h2 class="card-title">Program Code</h2>
                <div class="card-body" data-filter-type="program-code">
                    <div class="dropdown-wrapper">
                        <div class="select-btn">
                            <span class="btn-text">All</span>
                            <span class="arrow-dwn">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                        <ul class="list-items">
                            <!-- "All" Option -->
                            <li class="item">
                                <span class="checkbox">
                                    <i class="fa-solid fa-check check-icon"></i>
                                </span>
                                <span class="item-text">All</span>
                            </li>

                            <!-- Dynamic Items from Database -->
                            @foreach ($programs as $program)
                            <li class="item" data-value="{{ $program->program_code }}">
                                <span class="checkbox">
                                    <i class="fa-solid fa-check check-icon"></i>
                                </span>
                                <span class="item-text">{{ $program->program_code }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div id="filter-interview-status" class="card small-card filter-card">
                <h2 class="card-title">Interview Status</h2>
                <div class="card-body" data-filter-type="interview-status">
                    <div class="dropdown-wrapper">
                        <div class="select-btn">
                            <span class="btn-text">All</span>
                            <span class="arrow-dwn">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                        <ul class="list-items">
                            <!-- "All" Option -->
                            <li class="item" data-value="all">
                                <span class="checkbox">
                                    <i class="fa-solid fa-check check-icon"></i>
                                </span>
                                <span class="item-text">All</span>
                            </li>

                            <!-- Dynamic Items for Interview Status -->
                            @foreach ($interviewStatusOptions as $status)
                            <li class="item" data-value="{{ $status }}">
                                <span class="checkbox">
                                    <i class="fa-solid fa-check check-icon"></i>
                                </span>
                                <span class="item-text">{{ $status }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div id="total-interview" class="card small-card total-card">
                <script>
                    window.adminTotalInterviewURL = "{{ route('getFilterTotalInterviewOfUser') }}";
                </script>
                <h2 class="card-title">Total Interview</h2>
                <div class="card-body">
                    <div class="total-interview-count">
                        <span class="count">{{ number_format($totalInterviews) }}</span>
                    </div>
                </div>
            </div>

            <div id="interview-calendar-card" class="card wide-card calendar-card">
                <script>
                    window.adminInterviewCalendarURL = "{{ route('getInterviewSchedulesOfUser') }}";
                </script>
                <h2 class="card-title">Interview Calendar</h2>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>

            <div id="interview-table-card" class="card wide-card table-card">
                <h2 class="card-title">Interview Table</h2>
                <div class="card-body">
                    <div id="interview-kanban" class="kanban-board"></div>
                </div>
            </div>

            <div id="interviewModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="document.getElementById('interviewModal').style.display='none'">&times;</span>
                    <h2 id="interviewModalTitle">Interview Details</h2>
                    <div id="interviewDetails"></div>
                </div>
            </div>
        </div>
</body>

</html>