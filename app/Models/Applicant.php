<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $table = 'applicants'; // Explicit table name
    protected $primaryKey = 'applicant_id';
    public $timestamps = true; // Enable timestamps

    protected $fillable = [
        'appform_id',
        'academic_year_id',
        'status',
        'last_name',
        'first_name',
        'middle_name',
        'extension_name',
        'date_of_birth',
        'place_of_birth',
        'sex',
        'age',
        'blood_type',
        'citizenship',
        'civil_status',
        'religion',
        'birth_order',
        'no_of_siblings',
        'created_at',
        'updated_at'
    ];

    // Define status options
    const STATUS_OPTIONS = [
        'Pending',
        'Received',
        'Under Review',
        'Approved',
        'For Interview',
        'For Test',
        'Accepted',
        'Not Accepted'
    ];

    // Relationship: An applicant belongs to an academic year
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    // Relationship: An applicant can have many program applications
    public function programApplications()
    {
        return $this->hasMany(ProgramApplication::class, 'applicant_id');
    }


    // Relationship: An applicant can have one admission result
    public function admissionResult()
    {
        return $this->hasOne(AdmissionResult::class, 'applicant_id', 'applicant_id');
    }

    // Relationship: An applicant can have many responses
    public function applicantResponses()
    {
        return $this->hasMany(ApplicantResponse::class, 'applicant_id', 'applicant_id');
    }
}
