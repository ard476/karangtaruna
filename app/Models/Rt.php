<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rt extends Model
{
    protected $fillable = [
        'organization_id',
        'number',
        'name',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function label(): string
    {
        return 'RT '.$this->number.($this->name ? ' ('.$this->name.')' : '');
    }
}
