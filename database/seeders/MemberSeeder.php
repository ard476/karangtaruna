<?php

namespace Database\Seeders;

use App\Enums\MemberStatus;
use App\Models\Member;
use App\Models\Rt;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['rt' => '02', 'nama' => 'Siti Aminah', 'jk' => 'P'],
            ['rt' => '02', 'nama' => 'Andi Pratama', 'jk' => 'L'],
            ['rt' => '03', 'nama' => 'Dewi Lestari', 'jk' => 'P'],
            ['rt' => '03', 'nama' => 'Rizki Maulana', 'jk' => 'L'],
        ];

        foreach ($samples as $row) {
            $rt = Rt::where('number', $row['rt'])->first();

            if (! $rt) {
                continue;
            }

            Member::firstOrCreate(
                ['nama_lengkap' => $row['nama'], 'rt_id' => $rt->id],
                [
                    'jenis_kelamin' => $row['jk'],
                    'status' => MemberStatus::Aktif,
                    'bergabung_pada' => now()->subYear(),
                ]
            );
        }
    }
}
