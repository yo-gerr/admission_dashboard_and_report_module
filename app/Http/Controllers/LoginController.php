<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Applicant;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // Ensure this matches your login view path
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and the password matches (direct comparison)
        if ($user && $request->password === $user->password) {
            // Log the user in manually
            Auth::login($user);

            // Check if the user matches an applicant based on first, middle, and last name
            $applicant = Applicant::where('first_name', $user->first_name)
                ->where('middle_name', $user->middle_name)
                ->where('last_name', $user->last_name)
                ->first();

            // Redirect based on role or applicant status
            if ($applicant) {
                return redirect()->route('student.dashboard'); // Student applicant dashboard
            }

            switch ($user->role->role_abbreviation) {
                case 'AA':
                    return redirect()->route('admin.dashboard');
                case 'PH': 
                    return redirect()->route('program.dashboard');
                case 'FF': 
                    return redirect()->route('interviewer.dashboard');
                default:
                    return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
