<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'repair_id',
        'status',
        'description',
        'updated_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RepairStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RepairLogPhoto::class);
    }
}
