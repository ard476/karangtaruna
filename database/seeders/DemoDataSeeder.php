<?php

namespace Database\Seeders;

use App\Enums\ActivityStatus;
use App\Enums\AttendanceStatus;
use App\Enums\DuePaymentStatus;
use App\Enums\MemberStatus;
use App\Enums\TransactionType;
use App\Models\Activity;
use App\Models\ActivityAttendance;
use App\Models\Announcement;
use App\Models\DuePeriod;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\User;
use App\Services\DuePeriodService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $ketua = User::where('username', 'ketua')->first();

        $activity = Activity::create([
            'judul' => 'Kerja Bakti Lingkungan',
            'deskripsi' => 'Gotong royong membersihkan area RW dan saluran air.',
            'lokasi' => 'Balai RW 01',
            'mulai_pada' => now()->addDays(7)->setTime(7, 0),
            'selesai_pada' => now()->addDays(7)->setTime(11, 0),
            'status' => ActivityStatus::Dijadwalkan,
            'created_by' => $ketua?->id,
        ]);

        foreach (Member::where('status', MemberStatus::Aktif)->get() as $member) {
            ActivityAttendance::create([
                'activity_id' => $activity->id,
                'member_id' => $member->id,
                'status' => AttendanceStatus::TidakHadir,
            ]);
        }

        Activity::create([
            'judul' => 'Pelatihan Keterampilan Pemuda',
            'deskripsi' => 'Pelatihan komputer dasar untuk anggota.',
            'lokasi' => 'Aula Dusun',
            'mulai_pada' => now()->addDays(21)->setTime(9, 0),
            'status' => ActivityStatus::Dijadwalkan,
            'created_by' => $ketua?->id,
        ]);

        Transaction::create([
            'tipe' => TransactionType::Pemasukan,
            'kategori' => 'donasi',
            'jumlah' => 500000,
            'keterangan' => 'Donasi warga untuk kegiatan tahunan',
            'tanggal' => now()->subDays(5),
            'created_by' => $ketua?->id,
        ]);

        Transaction::create([
            'tipe' => TransactionType::Pengeluaran,
            'kategori' => 'operasional',
            'jumlah' => 150000,
            'keterangan' => 'ATK dan konsumsi rapat',
            'tanggal' => now()->subDays(3),
            'created_by' => $ketua?->id,
        ]);

        $period = DuePeriod::create([
            'judul' => 'Iuran Bulan '.now()->translatedFormat('F Y'),
            'jumlah' => 10000,
            'jatuh_tempo' => now()->addDays(14),
            'is_active' => true,
            'created_by' => $ketua?->id,
        ]);

        app(DuePeriodService::class)->generatePaymentsForActiveMembers($period);

        $period->payments()->whereHas('member', fn ($q) => $q->where('nama_lengkap', 'Budi Santoso'))->update([
            'status' => DuePaymentStatus::Lunas,
            'jumlah_bayar' => 10000,
            'dibayar_pada' => now()->subDay(),
            'metode' => 'tunai',
        ]);

        Announcement::create([
            'judul' => 'Jadwal Rapat Pengurus',
            'isi' => "Diinformasikan kepada seluruh pengurus akan diadakan rapat koordinasi.\n\nHari: Sabtu\nWaktu: 19.00 WIB\nTempat: Balai RW\n\nMohon kehadiran tepat waktu.",
            'is_published' => true,
            'published_at' => now(),
            'created_by' => $ketua?->id,
        ]);

        Announcement::create([
            'judul' => 'Pembayaran Iuran Bulanan',
            'isi' => 'Anggota diharapkan melunasi iuran bulan berjalan sebelum jatuh tempo. Hubungi bendahara untuk konfirmasi pembayaran.',
            'is_published' => true,
            'published_at' => now()->subDay(),
            'created_by' => $ketua?->id,
        ]);
    }
}
