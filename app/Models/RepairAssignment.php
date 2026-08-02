<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairAssignment extends Model
{
    protected $fillable = [
        'repair_id',
        'technician_id',
        'assigned_by',
        'assigned_at',
        'ended_at',
        'ended_by',
        'end_reason',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'technician_id',
        );
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_by',
        );
    }

    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'ended_by',
        );
    }
}
