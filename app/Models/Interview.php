<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'tbl_interview'; // Ensure it matches MySQL
    protected $primaryKey = 'interview_id';
    public $timestamps = true;

    protected $fillable = [
        'applicant_id',
        'program_id',
        'user_id',
        'room_id',
        'date_time',
        'mode',
        'scores',
        'status',
        'feedback',
        'created_at',
        'deleted_at'
    ];

    const STATUS_OPTIONS = [
        'Scheduled',
        'Completed',
        'Cancelled',
    ];

    protected $dates = ['deleted_at']; // Enable soft deletion handling

    // Relationship: An interview belongs to an applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'applicant_id');
    }

    // Relationship: An interview is linked to a program
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    // Relationship: An interview is facilitated by a user (faculty)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
