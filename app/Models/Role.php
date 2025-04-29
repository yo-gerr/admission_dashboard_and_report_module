<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'role_id';
    protected $fillable = ['role_name', 'role_abbreviation'];

    // Relationship: A role can have many users
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
