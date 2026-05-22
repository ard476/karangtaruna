<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MemberStatus;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\DuePeriod;
use App\Models\Member;
use App\Models\Organization;
use App\Services\FinanceService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private FinanceService $finance) {}

    public function __invoke(): View
    {
        $organization = Organization::with('rts')->first();

        return view('admin.dashboard', [
            'organization' => $organization,
            'stats' => [
                'anggota_aktif' => Member::where('status', MemberStatus::Aktif)->count(),
                'total_rt' => $organization?->rts->count() ?? 0,
                'kegiatan_aktif' => Activity::where('status', 'dijadwalkan')->where('mulai_pada', '>=', now())->count(),
                'pengumuman' => Announcement::published()->count(),
                'saldo' => $this->finance->saldo(),
                'iuran_aktif' => DuePeriod::where('is_active', true)->count(),
            ],
        ]);
    }
}
