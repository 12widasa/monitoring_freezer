<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairComponent extends Model
{
    protected $fillable = [
        'repair_id',
        'component_id',
        'quantity',
        'status',
        'note',
        'added_by',
        'installed_by',
        'installed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'installed_at' => 'datetime',
        ];
    }

    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Component::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function installedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'installed_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(RepairComponentLog::class);
    }
}
