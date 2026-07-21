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
            self::QUEUED => 'Dalam Antrean',
            self::INSPECTING => 'Pemeriksaan Kerusakan',
            self::REPAIRING => 'Proses Perbaikan',
            self::COMPLETED => 'Selesai',
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
