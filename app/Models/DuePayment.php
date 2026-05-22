<?php

namespace App\Models;

use App\Enums\DuePaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuePayment extends Model
{
    protected $fillable = [
        'due_period_id',
        'member_id',
        'jumlah_bayar',
        'dibayar_pada',
        'status',
        'metode',
        'catatan',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_bayar' => 'decimal:2',
            'dibayar_pada' => 'date',
            'status' => DuePaymentStatus::class,
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(DuePeriod::class, 'due_period_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
