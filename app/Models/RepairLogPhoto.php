<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairLogPhoto extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'repair_log_id',
        'photo_path',
        'sort_order',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function repairLog(): BelongsTo
    {
        return $this->belongsTo(RepairLog::class);
    }
}
