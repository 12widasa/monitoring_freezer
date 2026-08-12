<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'name',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'last_active_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function assignedRepairs(): HasMany
    {
        return $this->hasMany(Repair::class, 'technician_id');
    }

    public function repairAssignments(): HasMany
    {
        return $this->hasMany(
            RepairAssignment::class,
            'technician_id',
        );
    }

    public function createdRepairAssignments(): HasMany
    {
        return $this->hasMany(
            RepairAssignment::class,
            'assigned_by',
        );
    }

    public function endedRepairAssignments(): HasMany
    {
        return $this->hasMany(
            RepairAssignment::class,
            'ended_by',
        );
    }
}
