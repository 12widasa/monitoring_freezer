<?php

namespace App\Enums;

enum RepairStatus: string
{
    case QUEUED = 'queued';
    case INSPECTING = 'inspecting';
    case REPAIRING = 'repairing';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => 'Menunggu diperiksa',
            self::INSPECTING => 'Sedang diperiksa',
            self::REPAIRING => 'Sedang diperbaiki',
            self::COMPLETED => 'Perbaikan selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::QUEUED => 'gray',
            self::INSPECTING => 'blue',
            self::REPAIRING => 'yellow',
            self::COMPLETED => 'green',
        };
    }
}
