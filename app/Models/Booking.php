<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'name',
        'address',
        'phone',
        'sim_image',
        'payment_method',
        'payment_proof',
        'status',
        'total_payment',
        'review',
        'rating',
        'start_date',
        'end_date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function getDisplayStatusAttribute()
    {
        if ($this->is_expired) {
            return 'EXPIRED';
        }

        if ($this->status === 'Disetujui' && Carbon::parse($this->end_date)->isPast()) {
            return 'COMPLETED';
        }

        return $this->status;
    }

    /**
     * Accessor untuk cek apakah bisa dibatalkan
     */
    public function getCanBeCancelledAttribute()
    {
        return $this->status === 'Disetujui'
            && !$this->is_expired
            && Carbon::parse($this->end_date)->isFuture();
    }

    /**
     * Accessor untuk cek apakah bisa diselesaikan
     */
    public function getCanBeCompletedAttribute()
    {
        return $this->status === 'Disetujui'
            && Carbon::parse($this->end_date)->isPast()
            && !$this->is_expired;
    }

    /**
     * Accessor untuk cek apakah sudah expired (lebih dari 24 jam dari created_at)
     */
    public function getIsExpiredAttribute()
    {
        // Hitung 24 jam dari created_at
        $expiryTime = Carbon::parse($this->created_at)->addHours(24);
        return Carbon::now()->gt($expiryTime);
    }

    /**
     * Accessor untuk cek waktu tersisa
     */
    public function getTimeRemainingAttribute()
    {
        if ($this->is_expired) {
            return null;
        }

        $expiryTime = Carbon::parse($this->created_at)->addHours(24);
        return Carbon::now()->diff($expiryTime);
    }

    /**
     * Accessor untuk mendapatkan waktu expiry
     */
    public function getExpiresAtAttribute()
    {
        return Carbon::parse($this->created_at)->addHours(24);
    }
}
