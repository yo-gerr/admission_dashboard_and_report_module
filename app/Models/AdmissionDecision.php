<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionDecision extends Model
{
    use HasFactory;

    protected $primaryKey = 'decision_id';
    protected $fillable = [
        'result_id', 'decision_type', 'decision_letter_content', 'decision_date'
    ];

    // Relationship: An admission decision belongs to an admission result
    public function admissionResult()
    {
        return $this->belongsTo(AdmissionResult::class, 'result_id', 'result_id');
    }

    // Relationship: An admission decision can have many applicant responses
    public function applicantResponses()
    {
        return $this->hasMany(ApplicantResponse::class, 'decision_id', 'decision_id');
    }
}
