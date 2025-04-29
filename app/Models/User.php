<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    protected $fillable = [
        'first_name', 'last_name', 'middle_name', 'extension_name', 'contact_number',
        'email', 'password', 'role_id', 'status', 'date_of_birth', 'place_of_birth', 
        'sex', 'age', 'citizenship', 'address', 'blood_type', 'civil_status', 'religion',
        'birth_order', 'no_of_siblings', 'profile_picture', 'email_verified'
    ];

    // Relationship: A user belongs to a role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    // Relationship: A user may be an applicant (not all users are applicants)
    public function applicant()
    {
        return $this->hasOne(Applicant::class, 'user_id', 'user_id');
    }
}
