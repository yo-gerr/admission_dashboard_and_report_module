<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\ApplicantResponse;
use App\Models\AdmissionResult;
use App\Models\Interview;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function filtersOptions(Request $request)
    {
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->get();
        $statusOptions = Applicant::STATUS_OPTIONS;
        $programs = Program::select('program_code')->orderBy('program_code', 'asc')->get(); // Fetch program codes
        $totalApplicants = Applicant::count();
        $interviewStatusOptions = Interview::STATUS_OPTIONS;
        $currentUserId = Auth::id();
        $totalInterviews = Interview::where('user_id', $currentUserId)->count();
        $view = '';

        // Detect which dashboard to return
        if ($request->route()->getName() === 'program.dashboard') {
            $view = 'program_coordinator_dashboard';
        } else if ($request->route()->getName() === 'admin.dashboard') {
            $view = 'admission_administrator_dashboard';
        } else if ($request->route()->getName() === 'interviewer.dashboard') {
            $view = 'interviewer_dashboard';
        }

        return view($view, compact('academicYears', 'statusOptions', 'programs', 'totalApplicants', 'interviewStatusOptions', 'totalInterviews'));
    }

    public function filterTotalApplicants(Request $request)
    {
        try {
            // Start with applicants table, ensuring it's joined to program_applications
            $query = Applicant::select('applicants.*')
                ->leftJoin('program_applications', 'applicants.applicant_id', '=', 'program_applications.applicant_id');

            $academicYearIds = $request->academic_year_ids ?? [];
            $statusOptions = $request->status_options ?? [];
            $programCodes = $request->program_codes ?? [];

            // Filter by academic year
            if (!empty($academicYearIds) && !in_array('all', $academicYearIds)) {
                $query->whereIn('applicants.academic_year_id', $academicYearIds);
            }

            // Filter by application status
            if (!empty($statusOptions) && !in_array('all', $statusOptions)) {
                $query->whereIn('applicants.status', $statusOptions);
            }

            // Filter by program choice (now correctly linked through JOIN)
            if (!empty($programCodes) && !in_array('all', $programCodes)) {
                $query->whereIn('program_applications.first_choice', $programCodes);
            }

            $totalApplicants = $query->count();

            return response()->json(['total' => $totalApplicants]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getApplicantsPerAcademicYear()
    {
        try {
            $applicantsPerYear = Applicant::selectRaw('academic_years.start_year, academic_years.end_year, COUNT(*) as total')
                ->join('academic_years', 'applicants.academic_year_id', '=', 'academic_years.academic_year_id')
                ->groupBy('academic_years.start_year', 'academic_years.end_year')
                ->orderByDesc('academic_years.start_year')
                ->get()
                ->map(function ($item) {
                    $item->label = "{$item->start_year}-{$item->end_year}";
                    return $item;
                });

            return response()->json(['data' => $applicantsPerYear]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getProgramCapacityAndDemand(Request $request)
    {
        try {
            $academicYearIds = $request->academic_year_ids ?? [];
            $programCodes = $request->program_codes ?? [];

            $query = Program::select('programs.program_code', 'programs.max_students')
                ->leftJoin('program_applications', 'programs.program_code', '=', 'program_applications.first_choice')
                ->leftJoin('applicants', 'program_applications.applicant_id', '=', 'applicants.applicant_id')
                ->when(!empty($academicYearIds) && !in_array('all', $academicYearIds), function ($q) use ($academicYearIds) {
                    $q->whereIn('applicants.academic_year_id', $academicYearIds);
                })
                ->when(!empty($programCodes) && !in_array('all', $programCodes), function ($q) use ($programCodes) {
                    $q->whereIn('programs.program_code', $programCodes);
                })
                ->selectRaw('COUNT(program_applications.applicant_id) as total_applicants')
                ->groupBy('programs.program_code', 'programs.max_students')
                ->get()
                ->map(function ($program) {
                    return [
                        'label' => $program->program_code,
                        'capacity' => $program->max_students,
                        'demand' => $program->total_applicants
                    ];
                });

            return response()->json(['data' => $query]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getApplicantStatistics(Request $request)
    {
        try {
            $academicYearIds = $request->input('academic_year_ids', []);

            $applicantQuery = Applicant::query();
            $responseQuery = \App\Models\ApplicantResponse::query();

            if (!empty($academicYearIds)) {
                $applicantQuery->whereIn('academic_year_id', $academicYearIds);
                $responseQuery->whereHas('applicant', function ($query) use ($academicYearIds) {
                    $query->whereIn('academic_year_id', $academicYearIds);
                });
            }

            $totalApplicants = $applicantQuery->count();
            $admittedApplicants = (clone $applicantQuery)->where('status', 'Accepted')->count();
            $enrolledApplicants = $responseQuery->where('response_status', 'Accepted')->count();

            return response()->json([
                'total_applicants' => $totalApplicants,
                'admitted_applicants' => $admittedApplicants,
                'enrolled_applicants' => $enrolledApplicants
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getAdmissionTestPerformance(Request $request)
    {
        try {
            $academicYearIds = $request->academic_year_ids ?? [];
            $passingScore = 75;

            $query = AdmissionResult::with('applicant');

            // Filter by academic year via the related applicants table
            if (!empty($academicYearIds) && !in_array('all', $academicYearIds)) {
                $query->whereHas('applicant', function ($q) use ($academicYearIds) {
                    $q->whereIn('academic_year_id', $academicYearIds);
                });
            }

            $passedApplicants = (clone $query)->where('test_score', '>=', $passingScore)->count();
            $failedApplicants = (clone $query)->where('test_score', '<', $passingScore)->count();

            return response()->json([
                'passed_applicants' => $passedApplicants,
                'failed_applicants' => $failedApplicants
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getAdmittedNonEnrolledAnalysis(Request $request)
    {
        try {
            $academicYearIds = $request->academic_year_ids ?? [];

            // Filter applicants based on academic year
            $query = Applicant::where('status', 'Accepted');

            if (!empty($academicYearIds) && !in_array('all', $academicYearIds)) {
                $query->whereIn('academic_year_id', $academicYearIds);
            }

            // Count admitted applicants
            $admittedApplicants = $query->count();

            // Count enrolled applicants, ensuring filtering by academic year
            $enrolledApplicants = ApplicantResponse::where('response_status', 'Accepted')
                ->whereHas('applicant', function ($q) use ($academicYearIds) {
                    if (!empty($academicYearIds) && !in_array('all', $academicYearIds)) {
                        $q->whereIn('academic_year_id', $academicYearIds);
                    }
                })
                ->count();

            // Calculate non-enrolled applicants
            $nonEnrolledApplicants = $admittedApplicants - $enrolledApplicants;

            return response()->json([
                'admitted_applicants' => $admittedApplicants ?? 0,
                'enrolled_applicants' => $enrolledApplicants ?? 0,
                'non_enrolled_applicants' => max($nonEnrolledApplicants, 0)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getProgramPerformanceTrends(Request $request)
    {
        try {
            $programCodes = $request->program_codes ?? [];

            $query = Program::select('programs.program_code', 'academic_years.start_year', 'academic_years.end_year')
                ->leftJoin('program_applications', 'programs.program_code', '=', 'program_applications.first_choice')
                ->leftJoin('applicants', 'program_applications.applicant_id', '=', 'applicants.applicant_id')
                ->leftJoin('academic_years', 'applicants.academic_year_id', '=', 'academic_years.academic_year_id')
                ->when(!empty($programCodes) && !in_array('all', $programCodes), function ($q) use ($programCodes) {
                    $q->whereIn('programs.program_code', $programCodes);
                })
                ->selectRaw('programs.program_code, COUNT(program_applications.applicant_id) as total_applicants')
                ->groupBy('programs.program_code', 'academic_years.start_year', 'academic_years.end_year')
                ->orderBy('academic_years.start_year', 'asc')
                ->get()
                ->groupBy('program_code');

            if ($query->isEmpty()) {
                return response()->json(['error' => 'No data found'], 404);
            }

            $formattedData = $query->map(function ($programData, $programCode) {
                return [
                    'label' => $programCode,
                    'trend' => $programData->map(fn($item) => [
                        'year' => "{$item->start_year} - {$item->end_year}",
                        'applicants' => $item->total_applicants ?? 0
                    ])->toArray()
                ];
            });

            return response()->json(['data' => $formattedData]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getInterviewSchedules(Request $request)
    {
        try {
            $programCodes = $request->program_codes ?? ['all'];
            $interviewStatuses = $request->interview_statuses ?? ['all'];

            $query = Interview::with(['applicant', 'user', 'program'])
                ->orderBy('date_time', 'asc');

            if (!in_array('all', $programCodes)) {
                $query->whereHas('program', function ($q) use ($programCodes) {
                    $q->whereIn('program_code', $programCodes);
                });
            }

            if (!in_array('all', $interviewStatuses)) {
                $query->whereIn('status', $interviewStatuses);
            }

            $interviews = $query->get();

            $events = $interviews->map(function ($interview) {
                $statusColor = match ($interview->status) {
                    'Completed' => '#28a745',
                    'Cancelled' => '#dc3545',
                    'Scheduled' => '#ffc107',
                    default => '#6c757d',
                };

                return [
                    'id' => $interview->interview_id,
                    'title' => isset($interview->applicant)
                        ? "{$interview->applicant->last_name}, {$interview->applicant->first_name} ({$interview->mode})"
                        : "Unknown Applicant ({$interview->mode})",
                    'start' => $interview->date_time,
                    'color' => $statusColor,
                    'extendedProps' => [
                        'status' => $interview->status,
                        'user_name' => isset($interview->user)
                            ? "{$interview->user->first_name} {$interview->user->last_name}"
                            : "Unknown User",
                        'room_id' => $interview->room_id,
                        'program' => optional($interview->program)->program_name,
                        'mode' => $interview->mode,
                        'applicant_name' => isset($interview->applicant)
                            ? "{$interview->applicant->first_name} {$interview->applicant->last_name}"
                            : "Unknown Applicant"
                    ]
                ];
            });

            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function getTotalInterviewSchedulesOfUser(Request $request)
    {
        try {
            $programCodes = $request->program_codes ?? ['all'];
            $interviewStatuses = $request->interview_statuses ?? ['all'];
            $currentUserId = Auth::id(); // Get logged-in user's ID

            // Initialize base query
            $query = Interview::where('user_id', $currentUserId);

            // Filter by program codes
            if (!in_array('all', $programCodes)) {
                $query->whereHas('program', function ($q) use ($programCodes) {
                    $q->whereIn('program_code', $programCodes);
                });
            }

            // Filter by interview status
            if (!in_array('all', $interviewStatuses)) {
                $query->whereIn('status', $interviewStatuses);
            }

            // Return count only
            $totalInterviews = $query->count();

            return response()->json(['total_interviews' => $totalInterviews]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getInterviewSchedulesOfUser(Request $request)
    {
        try {
            $programCodes = $request->program_codes ?? ['all'];
            $interviewStatuses = $request->interview_statuses ?? ['all'];
            $currentUserId = Auth::id(); // Get logged-in user's ID

            // Fetch interviews for the logged-in user
            $query = Interview::with(['applicant', 'user', 'program'])
                ->where('user_id', $currentUserId) // Filter by logged-in user's ID
                ->orderBy('date_time', 'asc');

            // Apply program filtering only if a specific program is selected
            if (!in_array('all', $programCodes)) {
                $query->whereHas('program', function ($q) use ($programCodes) {
                    $q->whereIn('program_code', $programCodes);
                });
            }

            // Apply interview status filtering if a specific status is selected
            if (!in_array('all', $interviewStatuses)) {
                $query->whereIn('status', $interviewStatuses);
            }

            $interviews = $query->get();

            // Format response for FullCalendar
            $events = $interviews->map(function ($interview) {
                $statusColor = match ($interview->status) {
                    'Completed' => '#28a745', // Green
                    'Cancelled' => '#dc3545', // Red
                    'Scheduled' => '#ffc107', // Yellow
                    default => '#6c757d', // Gray (Other)
                };

                return [
                    'id' => $interview->interview_id,
                    'title' => isset($interview->applicant)
                        ? "{$interview->applicant->last_name}, {$interview->applicant->first_name} ({$interview->mode})"
                        : "Unknown Applicant ({$interview->mode})",
                    'start' => $interview->date_time,
                    'color' => $statusColor,
                    'extendedProps' => [
                        'status' => $interview->status,
                        'user_name' => isset($interview->user)
                            ? "{$interview->user->first_name} {$interview->user->last_name}"
                            : "Unknown User",
                        'room_id' => $interview->room_id,
                        'program' => optional($interview->program)->program_name,
                        'mode' => $interview->mode,
                        'applicant_name' => isset($interview->applicant)
                            ? "{$interview->applicant->first_name} {$interview->applicant->last_name}"
                            : "Unknown Applicant"
                    ]
                ];
            });

            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
}
