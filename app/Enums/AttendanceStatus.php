<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Hadir = 'hadir';
    case TidakHadir = 'tidak_hadir';
    case Izin = 'izin';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::TidakHadir => 'Tidak Hadir',
            self::Izin => 'Izin',
        };
    }
}
