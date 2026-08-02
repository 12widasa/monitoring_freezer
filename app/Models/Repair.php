<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Repair extends Model
{
    protected $fillable = [
        'freezer_id',
        'service_intake_id',
        'technician_id',
        'admin_id',
        'status',
        'initial_analysis',
    ];

    protected function casts(): array
    {
        return [
            'status' => RepairStatus::class,
        ];
    }

    public function freezer(): BelongsTo
    {
        return $this->belongsTo(Freezer::class);
    }

    public function serviceIntake(): BelongsTo
    {
        return $this->belongsTo(ServiceIntake::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RepairAssignment::class)
            ->latest('assigned_at');
    }

    public function activeAssignment(): HasOne
    {
        return $this->hasOne(RepairAssignment::class)
            ->whereNull('ended_at')
            ->latestOfMany('assigned_at');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(RepairLog::class);
    }

    public function latestLog(): HasOne
    {
        return $this->hasOne(RepairLog::class)
            ->latestOfMany('created_at');
    }

    public function components(): HasMany
    {
        return $this->hasMany(RepairComponent::class);
    }
}
