<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentApplicantDashboardController;
use App\Http\Controllers\ReportsController;


Route::get('/', function () {
    return view('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard routes
Route::get('/student_applicant_dashboard', [StudentApplicantDashboardController::class, 'studentDashboard'])
    ->name('student.dashboard')
    ->middleware('auth');

Route::get('/admission_administrator_dashboard', [ReportsController::class, 'filtersOptions'])
    ->name('admin.dashboard');

Route::get('/program_coordinator_dashboard', [ReportsController::class, 'filtersOptions'])
    ->name('program.dashboard');

Route::get('/interviewer_dashboard', [ReportsController::class, 'filtersOptions'])
    ->name('interviewer.dashboard')
    ->middleware('auth');

Route::get('/get-applicant-status', [StudentApplicantDashboardController::class, 'fetchStatusStep'])
    ->name('get.applicant.status')
    ->middleware('auth');

Route::post('/get-filter-total-applicants', [ReportsController::class, 'filterTotalApplicants'])
    ->name('getFilterTotalApplicants');

Route::get('get-applicants-per-academic-year', [ReportsController::class, 'getApplicantsPerAcademicYear'])
    ->name('getApplicantsPerYear');

Route::post('/get-program-capacity-and-demand', [ReportsController::class, 'getProgramCapacityAndDemand'])
    ->name('getProgramCapacityAndDemand');

Route::post('/get-applicant-statistics', [ReportsController::class, 'getApplicantStatistics'])
    ->name('getApplicantStatistics');

Route::post('/get-test-performance', [ReportsController::class, 'getAdmissionTestPerformance'])
    ->name('getTestPerformance');

Route::post('/get-admitted-non-enrolled', [ReportsController::class, 'getAdmittedNonEnrolledAnalysis'])
    ->name('getAdmittedNonEnrolled');

Route::post('/get-program-performance-trends', [ReportsController::class, 'getProgramPerformanceTrends'])
    ->name('getProgramPerformanceTrends');

Route::post('/get-interview-schedules', [ReportsController::class, 'getInterviewSchedules'])
    ->name('getInterviewSchedules');

Route::post('/get-filter-total-interview-of-user', [ReportsController::class, 'getTotalInterviewSchedulesOfUser'])
    ->name('getFilterTotalInterviewOfUser')
    ->middleware('auth');

Route::post('/get-interview-schedules-of-user', [ReportsController::class, 'getInterviewSchedulesOfUser'])
    ->name('getInterviewSchedulesOfUser')
    ->middleware('auth');
