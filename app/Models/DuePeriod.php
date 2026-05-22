<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DuePeriod extends Model
{
    protected $fillable = [
        'judul',
        'jumlah',
        'jatuh_tempo',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'jatuh_tempo' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DuePayment::class);
    }

    public function lunasCount(): int
    {
        return $this->payments()->where('status', 'lunas')->count();
    }

    public function totalCount(): int
    {
        return $this->payments()->count();
    }
}
