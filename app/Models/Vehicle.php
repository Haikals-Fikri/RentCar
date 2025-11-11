<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'brand',
        'type',
        'status_vehicle',
        'plate_number',
        'seat',
        'transmission',
        'fuel_type',
        'year',
        'price_per_day',
        'image',
    ];

    // Relasi ke Owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class);
    }
}
