<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * 🔹 Relationship dengan UserProfile (untuk user biasa)
     */
    public function userProfile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    /**
     * 🔹 Relationship dengan OwnerProfile (untuk owner)
     */
    public function ownerProfile()
    {
        return $this->hasOne(OwnerProfile::class, 'owner_id');
    }

    /**
     * 🔹 Relationship dengan Vehicle (untuk owner) - YANG DITAMBAHKAN
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'owner_id');
    }

    /**
     * 🔹 Cek apakah user adalah owner
     */
    public function isOwner()
    {
        return $this->role === 'owner';
    }

    /**
     * 🔹 Cek apakah user adalah user biasa
     */
    public function isUser()
    {
        return $this->role === 'user';
    }
}
