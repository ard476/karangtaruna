<?php

namespace App\Models;

use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    protected $fillable = [
        'user_id',
        'rt_id',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'phone',
        'email',
        'status',
        'bergabung_pada',
        'photo_path',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'bergabung_pada' => 'date',
            'status' => MemberStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivityAttendance::class);
    }

    public function duePayments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DuePayment::class);
    }

    public function hasLogin(): bool
    {
        return $this->user_id !== null;
    }

    /**
     * @param  Builder<Member>  $query
     * @param  array{rt_id?: string, status?: string, q?: string}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['rt_id'] ?? null, fn (Builder $q, string $rtId) => $q->where('rt_id', $rtId));

        $query->when($filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status));

        $query->when($filters['q'] ?? null, function (Builder $q, string $search) {
            $term = '%'.$search.'%';
            $q->where(function (Builder $inner) use ($term) {
                $inner->where('nama_lengkap', 'ilike', $term)
                    ->orWhere('nik', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });
        });
    }
}
