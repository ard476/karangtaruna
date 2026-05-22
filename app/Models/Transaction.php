<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'tipe',
        'kategori',
        'jumlah',
        'keterangan',
        'tanggal',
        'bukti_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tipe' => TransactionType::class,
            'jumlah' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['tipe'] ?? null, fn (Builder $q, string $tipe) => $q->where('tipe', $tipe));
        $query->when($filters['kategori'] ?? null, fn (Builder $q, string $kat) => $q->where('kategori', $kat));
        $query->when($filters['dari'] ?? null, fn (Builder $q, string $dari) => $q->whereDate('tanggal', '>=', $dari));
        $query->when($filters['sampai'] ?? null, fn (Builder $q, string $sampai) => $q->whereDate('tanggal', '<=', $sampai));
    }

    public function kategoriLabel(): string
    {
        $key = $this->kategori;
        $lists = $this->tipe === TransactionType::Pemasukan
            ? config('finance.kategori_pemasukan', [])
            : config('finance.kategori_pengeluaran', []);

        return $lists[$key] ?? $key;
    }
}
