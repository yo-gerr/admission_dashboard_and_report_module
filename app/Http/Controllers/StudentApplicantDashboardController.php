<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Applicant;
use App\Models\College;

class StudentApplicantDashboardController extends Controller
{
    public function studentDashboard()
    {
        $user = Auth::user();

        // Fetch the applicant's status based on name matching
        $applicant = Applicant::where('first_name', $user->first_name)
            ->where('middle_name', $user->middle_name)
            ->where('last_name', $user->last_name)
            ->first();

        // Map status to progress step based on STATUS_OPTIONS
        $statusSteps = [
            'Pending' => 1, // 🕒 Application initiated
            'Received' => 2, // 📜 Application received and logged
            'Under Review' => 3, // 🔍 Application being reviewed
            'Approved' => 4, // ✅ Initial approval granted
            'For Interview' => 5, // 🎤 Interview appointment set
            'For Test' => 6, // 📅 Test scheduled
            'Accepted' => 7, // 🎓 Applicant accepted into the program
            'Not Accepted' => 8, // ❌ Application rejected after review
        ];

        $currentStep = $applicant ? ($statusSteps[$applicant->status] ?? 1) : 1;

        // Fetch college programs
        $colleges = College::with('programs')->get();

        return view('student_applicant_dashboard', compact('currentStep', 'colleges'));
    }

    public function fetchStatusStep()
{
    $user = Auth::user();

    $applicant = Applicant::where('first_name', $user->first_name)
        ->where('middle_name', $user->middle_name)
        ->where('last_name', $user->last_name)
        ->first();

    $statusSteps = [
        'Pending' => 1,
        'Received' => 2,
        'Under Review' => 3,
        'Approved' => 4,
        'For Interview' => 5,
        'For Test' => 6,
        'Accepted' => 7,
        'Not Accepted' => 8,
    ];

    $step = $applicant ? ($statusSteps[$applicant->status] ?? 1) : 1;

    return response()->json(['step' => $step]);
}

}
