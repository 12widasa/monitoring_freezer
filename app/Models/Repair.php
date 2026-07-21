<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $fillable = [
        'freezer_id',
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

    public function freezer()
    {
        return $this->belongsTo(Freezer::class);
    }

    public function logs()
    {
        return $this->hasMany(RepairLog::class);
    }
}
