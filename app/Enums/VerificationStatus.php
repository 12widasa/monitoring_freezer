<?php

namespace App\Enums;

enum VerificationStatus: string
{
    case PENDING_ARRIVAL = 'pending_arrival';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_ARRIVAL => 'Menunggu Fisik Tiba',
            self::VERIFIED => 'Terverifikasi',
            self::REJECTED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING_ARRIVAL => 'yellow',
            self::VERIFIED => 'green',
            self::REJECTED => 'red',
        };
    }
}
