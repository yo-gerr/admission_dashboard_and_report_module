<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionResult extends Model
{
    use HasFactory;

    protected $primaryKey = 'result_id';
    protected $fillable = [
        'applicant_id', 'admission_status', 'test_score',
        'interview_result', 'document_verification_status', 'decision_letter'
    ];

    // Relationship: An admission result belongs to an applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    // Relationship: An admission result can have one admission decision
    public function admissionDecision()
    {
        return $this->hasOne(AdmissionDecision::class, 'result_id', 'result_id');
    }
}
