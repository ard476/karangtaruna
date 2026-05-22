<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'superadmin';
    case Ketua = 'ketua';
    case Sekretaris = 'sekretaris';
    case Bendahara = 'bendahara';
    case Anggota = 'anggota';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Ketua => 'Ketua',
            self::Sekretaris => 'Sekretaris',
            self::Bendahara => 'Bendahara',
            self::Anggota => 'Anggota',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    public function isPengurus(): bool
    {
        return $this !== self::Anggota;
    }

    public function canAccessAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isPengurus();
    }

    /** Peran yang bisa dipilih saat membuat akun pengguna (bukan superadmin). */
    public static function assignable(): array
    {
        return [
            self::Ketua,
            self::Sekretaris,
            self::Bendahara,
            self::Anggota,
        ];
    }
}
