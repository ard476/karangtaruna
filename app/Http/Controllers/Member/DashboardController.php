<?php

namespace App\Http\Controllers\Member;

use App\Enums\ActivityStatus;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        $member = $user->member?->load('rt');

        $ongoingActivities = Activity::query()
            ->where('status', ActivityStatus::Dijadwalkan)
            ->where('mulai_pada', '<=', now())
            ->where(fn ($query) => $query
                ->whereNull('selesai_pada')
                ->orWhere('selesai_pada', '>=', now()))
            ->orderBy('mulai_pada')
            ->limit(3)
            ->get();

        $upcomingActivities = Activity::query()
            ->where('status', ActivityStatus::Dijadwalkan)
            ->where('mulai_pada', '>', now())
            ->orderBy('mulai_pada')
            ->limit(3)
            ->get();

        $announcements = Announcement::published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $unpaidDues = $member
            ? $member->duePayments()->with('period')->where('status', 'belum_bayar')->limit(5)->get()
            : collect();

        return view('member.dashboard', [
            'organization' => Organization::with('rts')->first(),
            'member' => $member,
            'ongoingActivities' => $ongoingActivities,
            'upcomingActivities' => $upcomingActivities,
            'announcements' => $announcements,
            'unpaidDues' => $unpaidDues,
        ]);
    }
}
