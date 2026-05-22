<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityShift extends Model
{
    protected $fillable = [
        'activity_id',
        'nama',
        'urutan',
        'mulai_pada',
        'selesai_pada',
        'lokasi',
        'latitude',
        'longitude',
        'radius_meters',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'mulai_pada' => 'datetime',
            'selesai_pada' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'radius_meters' => 'integer',
        ];
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ActivityShiftAttendance::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ActivityShiftAssignment::class);
    }

    public function assignedMembers(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'activity_shift_assignments')
            ->withTimestamps();
    }

    public function hasAssignments(): bool
    {
        if ($this->relationLoaded('assignments')) {
            return $this->assignments->isNotEmpty();
        }

        return $this->assignments()->exists();
    }

    public function isAssignedTo(Member|int $member): bool
    {
        $memberId = $member instanceof Member ? $member->id : $member;

        return $this->assignments()->where('member_id', $memberId)->exists();
    }

    public function isInAttendanceWindow(): bool
    {
        return $this->attendanceWindowPhase() === 'active';
    }

    /** @return 'before'|'active'|'after' */
    public function attendanceWindowPhase(): string
    {
        if (! $this->mulai_pada || $this->mulai_pada->isFuture()) {
            return 'before';
        }

        if ($this->selesai_pada && $this->selesai_pada->isPast()) {
            return 'after';
        }

        return 'active';
    }

    public function hadirCount(): int
    {
        return $this->attendances()->where('status', AttendanceStatus::Hadir)->count();
    }

    public function hasRadius(): bool
    {
        return $this->latitude !== null
            && $this->longitude !== null
            && $this->radius_meters !== null
            && $this->radius_meters > 0;
    }

    public function distanceTo(float $latitude, float $longitude): int
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad((float) $this->latitude);
        $lonFrom = deg2rad((float) $this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            (sin($latDelta / 2) ** 2)
            + cos($latFrom) * cos($latTo) * (sin($lonDelta / 2) ** 2)
        ));

        return (int) round($earthRadius * $angle);
    }
}
