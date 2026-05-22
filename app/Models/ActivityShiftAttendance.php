<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityShiftAttendance extends Model
{
    protected $fillable = [
        'activity_shift_id',
        'member_id',
        'status',
        'photo_path',
        'absen_latitude',
        'absen_longitude',
        'distance_meters',
        'keterangan',
        'absen_pada',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'absen_pada' => 'datetime',
            'absen_latitude' => 'float',
            'absen_longitude' => 'float',
            'distance_meters' => 'integer',
        ];
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(ActivityShift::class, 'activity_shift_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }
}
