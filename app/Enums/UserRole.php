<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TECHNICIAN = 'technician';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::TECHNICIAN => 'Teknisi',
            self::CUSTOMER => 'Pelanggan',
        };
    }
}
