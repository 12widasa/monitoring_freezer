<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairComponentLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'repair_component_id',
        'status',
        'quantity',
        'note',
        'updated_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function repairComponent(): BelongsTo
    {
        return $this->belongsTo(RepairComponent::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
