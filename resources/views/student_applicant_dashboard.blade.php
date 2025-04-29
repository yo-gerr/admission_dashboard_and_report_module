<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <!-- Inline Styling for Backup -->
    <style>

    </style>

    <link rel="stylesheet" href="{{ asset('css/student_applicant/student_applicant.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar_template/sidebar_template.css') }}">


    <!-- Include Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    @include('sidebar_template') <!-- Sidebar -->

    <!-- Main Content -->
    <div class="main-content">
        <section class="status-content-wrapper">
            <script>
                window.applicantStatusUrl = "{{ url('/get-applicant-status') }}";
            </script>
            <script src="{{ asset('js/student_applicant/student_applicant.js') }}" defer>
                window.currentStepFromServer = {
                    $currentStep
                };
            </script>
            <h1 class="status-title">Admission Status</h1>
            <div class="status-card">
                <div class="status-tracker">
                    <div class="steps" data-step="{{ $currentStep }}">
                        <div class="step">
                            <span class="circle">1</span>
                            <span class="label">Pending</span>
                        </div>
                        <div class="step">
                            <span class="circle">2</span>
                            <span class="label">Received</span>
                        </div>
                        <div class="step">
                            <span class="circle">3</span>
                            <span class="label">Under Review</span>
                        </div>
                        <div class="step">
                            <span class="circle">4</span>
                            <span class="label">Approved</span>
                        </div>
                        <div class="step">
                            <span class="circle">5</span>
                            <span class="label">For Interview</span>
                        </div>
                        <div class="step">
                            <span class="circle">6</span>
                            <span class="label">For Test</span>
                        </div>
                        <div class="step">
                            <span class="circle">7</span>
                            <span class="label">Accepted</span>
                        </div>
                        <div class="step">
                            <span class="circle">8</span>
                            <span class="label">Not Accepted</span>
                        </div>
                        <div class="progress-bar">
                            <span class="indicator"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="college-programs-content-wrapper">
            <h1 class="college-programs-title">College Programs</h1>
            @foreach ($colleges as $college)
            <div class="college-container">
                <h2 class="college-name {{ strtolower(str_replace(' ', '-', $college->name)) }}">{{ $college->name }}</h2>
                <ul class="program-list">
                    @foreach ($college->programs as $program)
                    <li>{{ $program->program_name }}</li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </section>
    </div>
</body>

</html>