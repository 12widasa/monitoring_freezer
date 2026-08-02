<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'address',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function freezers()
    {
        return $this->hasMany(Freezer::class);
    }

    public function repairs(): HasManyThrough
    {
        return $this->hasManyThrough(
            Repair::class,
            Freezer::class,
            'customer_id',
            'freezer_id',
            'id',
            'id',
        );
    }
}
