<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case Dijadwalkan = 'dijadwalkan';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Dijadwalkan => 'Dijadwalkan',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }
}
