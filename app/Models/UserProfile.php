<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class UserProfile extends Model
{
    protected $fillable = [
    'user_id',
    'full_name',
    'phone_number',
    'address',
    'city',
    'province',
    'postal_code',
    'date_of_birth',
    'gender',
    'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
