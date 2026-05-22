<?php

namespace Database\Seeders;

use App\Enums\MemberStatus;
use App\Enums\UserRole;
use App\Models\Member;
use App\Models\Rt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $pengurus = [
            ['username' => 'ketua', 'name' => 'Ketua Karang Taruna', 'role' => UserRole::Ketua],
            ['username' => 'sekretaris', 'name' => 'Sekretaris', 'role' => UserRole::Sekretaris],
            ['username' => 'bendahara', 'name' => 'Bendahara', 'role' => UserRole::Bendahara],
        ];

        foreach ($pengurus as $data) {
            User::create([
                'username' => $data['username'],
                'name' => $data['name'],
                'email' => $data['username'].'@karangtaruna.local',
                'password' => $password,
                'role' => $data['role'],
                'is_active' => true,
            ]);
        }

        $rt = Rt::where('number', '01')->first();

        $anggotaUser = User::create([
            'username' => 'anggota1',
            'name' => 'Budi Santoso',
            'email' => 'anggota1@karangtaruna.local',
            'password' => $password,
            'role' => UserRole::Anggota,
            'is_active' => true,
            'phone' => '081298765432',
        ]);

        Member::create([
            'user_id' => $anggotaUser->id,
            'rt_id' => $rt->id,
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'status' => MemberStatus::Aktif,
            'bergabung_pada' => now()->subYears(2),
            'phone' => '081298765432',
        ]);
    }
}
