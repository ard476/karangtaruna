<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Rt;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::create([
            'name' => 'Karang Taruna Dusun Suka Maju',
            'dusun' => 'Suka Maju',
            'rw_number' => '01',
            'rw_name' => null,
            'desa' => 'Contoh Desa',
            'kecamatan' => 'Contoh Kecamatan',
            'kabupaten' => 'Contoh Kabupaten',
            'alamat_lengkap' => 'Dusun Suka Maju, RW 01, Desa Contoh Desa',
            'tahun_berdiri' => 2010,
            'phone' => '081234567890',
            'email' => 'karangtaruna@example.com',
        ]);

        foreach (['01', '02', '03'] as $number) {
            Rt::create([
                'organization_id' => $organization->id,
                'number' => $number,
            ]);
        }
    }
}
