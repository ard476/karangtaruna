<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappAttendanceSession extends Model
{
    protected $fillable = [
        'activity_shift_id',
        'member_id',
        'phone',
        'latitude',
        'longitude',
        'distance_meters',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'distance_meters' => 'integer',
            'expires_at' => 'datetime',
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
}
