<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OwnerProfile extends Model
{
    protected $fillable = [
        'owner_id',
        'owner_name',
        'business_name',
        'phone_number',
        'address',
        'city',
        'province',
        'ktp_number',
        'npwp_number',
        'bank_account',
        'facebook',
        'instagram',
        'tiktok',
        'photo',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
