<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MemberStatus;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\DuePeriod;
use App\Models\Member;
use App\Models\Organization;
use App\Models\Transaction;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private FinanceService $finance) {}

    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->hasPermission('reports.view'), 403);

        $dari = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        return view('admin.reports.index', [
            'organization' => Organization::first(),
            'dari' => $dari,
            'sampai' => $sampai,
            'stats' => [
                'anggota_aktif' => Member::where('status', MemberStatus::Aktif)->count(),
                'kegiatan' => Activity::whereBetween('mulai_pada', [$dari, $sampai.' 23:59:59'])->count(),
                'pengumuman' => Announcement::published()->count(),
                'periode_iuran' => DuePeriod::where('is_active', true)->count(),
            ],
            'pemasukan' => $this->finance->totalPemasukan($dari, $sampai),
            'pengeluaran' => $this->finance->totalPengeluaran($dari, $sampai),
            'saldo' => $this->finance->saldo(),
            'transaksi_terbaru' => Transaction::orderByDesc('tanggal')->limit(10)->get(),
        ]);
    }
}
