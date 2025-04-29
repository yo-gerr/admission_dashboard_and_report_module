<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $primaryKey = 'program_id';
    protected $fillable = [
        'program_code',
        'program_name',
        'college_id',
        'description',
        'duration_years',
        'max_students',
        'status',
        'application_starting',
        'application_end'
    ];

    // Relationship: A program belongs to a college
    public function college()
    {
        return $this->belongsTo(College::class, 'college_id', 'college_id');
    }
}
