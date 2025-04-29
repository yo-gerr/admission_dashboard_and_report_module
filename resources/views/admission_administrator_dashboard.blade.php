<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/sidebar_template/sidebar_template.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reports/reports.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-funnel"></script>
    <script src="{{ asset('js/reports/filter.js') }}" defer></script>
    <script src="{{ asset('js/reports/masonry_layout.js') }}" defer></script>
    <script src="{{ asset('js/reports/admission_performance_chart.js') }}" defer></script>
    <script src="{{ asset('js/reports/program_capacity_demand_chart.js') }}" defer></script>
    <script src="{{ asset('js/reports/applicant_conversion_chart.js') }}" defer></script>
    <script src="{{ asset('js/reports/test_performance_chart.js') }}" defer></script>
    <script src="{{ asset('js/reports/admitted_non_enrolled_chart.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body data-view="admission_administrator_dashboard">
    @include('sidebar_template') <!-- Sidebar -->

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard">
            <div id="filter-academic-year" class="card small-card filter-card">
                <h2 class="card-title">Academic Year</h2>
                <div class="card-body" data-filter-type="academic-year">
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
                            @foreach ($academicYears as $year)
                            <li class="item" data-value="{{ $year->academic_year_id }}">
                                <span class="checkbox">
                                    <i class="fa-solid fa-check check-icon"></i>
                                </span>
                                <span class="item-text">{{ $year->start_year }} - {{ $year->end_year }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

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

            <div id="filter-status" class="card small-card filter-card">
                <h2 class="card-title">Application Status</h2>
                <div class="card-body" data-filter-type="status">
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

                            <!-- Dynamic Items: List of Status Options -->
                            @foreach ($statusOptions as $status)
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

            <div id="total-applicants" class="card small-card total-card">
                <script>
                    window.adminTotalApplicantsURL = "{{ route('getFilterTotalApplicants') }}";
                </script>
                <h2 class="card-title">Total Applicants</h2>
                <div class="card-body">
                    <div class="total-applicants-count">
                        <span class="count">{{ number_format($totalApplicants) }}</span>
                    </div>
                </div>
            </div>

            <div id="total-applicants-chart" class="card wide-card">
                <script>
                    window.adminAdmissionPerformanceChartURL = "{{ route('getApplicantsPerYear') }}";
                </script>
                <h2 class="card-title">Admission Performance</h2>
                <div class="card-body">
                    <canvas id="applicantsChart" height="100"></canvas>
                </div>
            </div>

            <div id="applicant-conversion-chart" class="card small-card">
                <script>
                    window.adminApplicantConversionChartURL = "{{ route('getApplicantStatistics') }}";
                </script>
                <h2 class="card-title">Applicant Conversion Chart</h2>
                <div class="card-body">
                    <canvas id="applicantConversionChart" height="100"></canvas>
                </div>
            </div>

            <div id="program-capacity-and-demand-chart" class="card small-card ">
                <script>
                    window.adminProgramCapacityDemandChartURL = "{{ route('getProgramCapacityAndDemand') }}";
                </script>
                <h2 class="card-title">Program Capacity and Demand</h2>
                <div class="card-body">
                    <canvas id="programCapacityDemandChart" height="100"></canvas>
                </div>
            </div>

            <div id="test-performance-chart" class="card small-card ">
                <script>
                    window.adminTestPerformanceChartURL = "{{ route('getTestPerformance') }}";
                </script>
                <h2 class="card-title">Admission Test Performance</h2>
                <div class="card-body">
                    <canvas id="testPerformanceChart" height="100"></canvas>
                </div>
            </div>

            <div id="admitted-non-enrolled-chart" class="card small-card ">
                <script>
                    window.adminAdmittedNonEnrolledChartURL = "{{ route('getAdmittedNonEnrolled') }}";
                </script>
                <h2 class="card-title">Admitted But Not Enrolled Analysis</h2>
                <div class="card-body">
                    <canvas id="admittedButNotEnrolledChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</body>

</html>