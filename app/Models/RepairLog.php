<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Model;

class RepairLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'repair_id',
        'status',
        'description',
        'photo_path',
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

    public function repair()
    {
        return $this->belongsTo(Repair::class);
    }
}
