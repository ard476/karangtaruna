<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityShiftAssignment extends Model
{
    protected $fillable = [
        'activity_shift_id',
        'member_id',
        'catatan',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(ActivityShift::class, 'activity_shift_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
