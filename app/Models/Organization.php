<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'dusun',
        'rw_number',
        'rw_name',
        'desa',
        'kecamatan',
        'kabupaten',
        'alamat_lengkap',
        'tahun_berdiri',
        'phone',
        'email',
        'logo_path',
    ];

    public function rts(): HasMany
    {
        return $this->hasMany(Rt::class);
    }

    public function rwLabel(): string
    {
        return 'RW '.$this->rw_number.($this->rw_name ? ' '.$this->rw_name : '');
    }

    public function wilayahLabel(): string
    {
        return $this->dusun.', '.$this->rwLabel().', Desa '.$this->desa;
    }
}
