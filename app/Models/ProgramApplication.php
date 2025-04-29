<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramApplication extends Model
{
    use HasFactory;

    protected $primaryKey = 'application_id';

    protected $fillable = [
        'applicant_id', 'first_choice', 'first_choice_reason',
        'second_choice', 'second_choice_reason'
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }
}
