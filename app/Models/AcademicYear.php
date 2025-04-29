<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $primaryKey = 'academic_year_id';
    protected $fillable = ['start_year', 'end_year', 'status'];

    // Relationship: An academic year can have many applicants
    public function applicants()
    {
        return $this->hasMany(Applicant::class, 'academic_year_id', 'academic_year_id');
    }

    // Relationship: An academic year can have many program applications
    public function programApplications()
    {
        return $this->hasMany(ProgramApplication::class, 'academic_year_id', 'academic_year_id');
    }

    // Relationship: An academic year can have many programs
    public function programs()
    {
        return $this->hasMany(Program::class, 'academic_year_id', 'academic_year_id');
    }

    // Relationship: An academic year can have many admission results
    public function admissionResults()
    {
        return $this->hasMany(AdmissionResult::class, 'academic_year_id', 'academic_year_id');
    }
}
