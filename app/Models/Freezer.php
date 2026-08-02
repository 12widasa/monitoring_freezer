<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'created_by',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    public function serviceIntakes(): HasMany
    {
        return $this->hasMany(ServiceIntake::class);
    }

    public function latestIntake(): HasOne
    {
        return $this->hasOne(ServiceIntake::class)
            ->latestOfMany('received_at');
    }

    public function latestRepair(): HasOne
    {
        return $this->hasOne(Repair::class)->latestOfMany();
    }
}
