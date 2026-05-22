<?php

namespace App\Enums;

enum TransactionType: string
{
    case Pemasukan = 'pemasukan';
    case Pengeluaran = 'pengeluaran';

    public function label(): string
    {
        return match ($this) {
            self::Pemasukan => 'Pemasukan',
            self::Pengeluaran => 'Pengeluaran',
        };
    }
}
