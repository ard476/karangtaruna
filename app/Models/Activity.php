<?php

namespace App\Models;

use App\Enums\ActivityKind;
use App\Enums\ActivityStatus;
use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'lokasi',
        'mulai_pada',
        'selesai_pada',
        'status',
        'tipe',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'mulai_pada' => 'datetime',
            'selesai_pada' => 'datetime',
            'status' => ActivityStatus::class,
            'tipe' => ActivityKind::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(ActivityShift::class)->orderBy('urutan')->orderBy('mulai_pada');
    }

    public function shiftAttendances(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            ActivityShiftAttendance::class,
            ActivityShift::class,
            'activity_id',
            'activity_shift_id',
            'id',
            'id'
        );
    }

    public function isHajatan(): bool
    {
        return $this->tipe === ActivityKind::Hajatan;
    }

    public function isInAttendanceWindow(): bool
    {
        return $this->status === ActivityStatus::Dijadwalkan
            && $this->mulai_pada?->lte(now())
            && ($this->selesai_pada === null || $this->selesai_pada->gte(now()));
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status));
        $query->when($filters['q'] ?? null, function (Builder $q, string $search) {
            $term = '%'.$search.'%';
            $q->where(fn (Builder $inner) => $inner
                ->where('judul', 'ilike', $term)
                ->orWhere('lokasi', 'ilike', $term));
        });
    }

    public function hadirCount(): int
    {
        if ($this->isHajatan()) {
            return $this->shiftAttendances()->where('status', AttendanceStatus::Hadir)->count();
        }

        return $this->attendances()->where('status', AttendanceStatus::Hadir)->count();
    }
}
