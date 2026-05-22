<?php

namespace App\Enums;

enum ActivityKind: string
{
    case Biasa = 'biasa';
    case Hajatan = 'hajatan';

    public function label(): string
    {
        return match ($this) {
            self::Biasa => 'Biasa',
            self::Hajatan => 'Hajatan (shift & foto)',
        };
    }
}
