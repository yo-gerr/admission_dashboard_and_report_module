<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;

    protected $primaryKey = 'college_id';
    protected $fillable = ['name', 'description'];

    // Relationship: A college can have many programs
    public function programs()
    {
        return $this->hasMany(Program::class, 'college_id', 'college_id');
    }
}
