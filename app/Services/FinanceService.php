<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Transaction;

class FinanceService
{
    public function saldo(): float
    {
        $masuk = (float) Transaction::where('tipe', TransactionType::Pemasukan)->sum('jumlah');
        $keluar = (float) Transaction::where('tipe', TransactionType::Pengeluaran)->sum('jumlah');

        return $masuk - $keluar;
    }

    public function totalPemasukan(?string $dari = null, ?string $sampai = null): float
    {
        return (float) Transaction::query()
            ->where('tipe', TransactionType::Pemasukan)
            ->when($dari, fn ($q) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal', '<=', $sampai))
            ->sum('jumlah');
    }

    public function totalPengeluaran(?string $dari = null, ?string $sampai = null): float
    {
        return (float) Transaction::query()
            ->where('tipe', TransactionType::Pengeluaran)
            ->when($dari, fn ($q) => $q->whereDate('tanggal', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal', '<=', $sampai))
            ->sum('jumlah');
    }
}
