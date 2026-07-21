<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Model;

class Freezer extends Model
{
    protected $fillable = [
        'customer_id',
        'brand',
        'model',
        'serial_number',
        'capacity_liter',
        'estimated_age',
        'photo_path',
        'status_verifikasi',
        'rejection_reason',
        'complaint_note',
        'created_by',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'status_verifikasi' => VerificationStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }
}
