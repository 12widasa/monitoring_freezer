<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Component extends Model
{
    protected $fillable = [
        'name',
        'part_number',
        'unit',
    ];

    public function repairComponents(): HasMany
    {
        return $this->hasMany(RepairComponent::class);
    }
}
