<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceIntake extends Model
{
    protected $fillable = [
        'freezer_id',
        'intake_code',
        'complaint_note',
        'condition_note',
        'status_verifikasi',
        'rejection_reason',
        'received_by',
        'received_at',
        'verified_by',
        'verified_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status_verifikasi' => VerificationStatus::class,
            'received_at' => 'datetime',
            'verified_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function freezer(): BelongsTo
    {
        return $this->belongsTo(Freezer::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'received_by',
        );
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'verified_by',
        );
    }

    public function repair(): HasOne
    {
        return $this->hasOne(Repair::class);
    }
}
