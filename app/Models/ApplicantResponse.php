<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantResponse extends Model
{
    use HasFactory;

    protected $primaryKey = 'response_id';
    protected $fillable = [
        'applicant_id', 'decision_id', 'response_status', 'response_date'
    ];

    // Relationship: A response belongs to an applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    // Relationship: A response belongs to an admission decision
    public function admissionDecision()
    {
        return $this->belongsTo(AdmissionDecision::class, 'decision_id', 'decision_id');
    }
}
