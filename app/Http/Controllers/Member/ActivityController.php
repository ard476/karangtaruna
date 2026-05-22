<?php

namespace App\Http\Controllers\Member;

use App\Enums\ActivityStatus;
use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreActivityAttendanceRequest;
use App\Models\Activity;
use App\Models\ActivityAttendance;
use App\Models\ActivityShiftAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('activities.view'), 403);

        $ongoingActivities = Activity::query()
            ->where('status', ActivityStatus::Dijadwalkan)
            ->where('mulai_pada', '<=', now())
            ->where(fn ($query) => $query
                ->whereNull('selesai_pada')
                ->orWhere('selesai_pada', '>=', now()))
            ->orderBy('mulai_pada')
            ->get();

        $upcomingActivities = Activity::query()
            ->where('status', ActivityStatus::Dijadwalkan)
            ->where('mulai_pada', '>', now())
            ->orderBy('mulai_pada')
            ->paginate(10);

        return view('member.activities.index', compact('ongoingActivities', 'upcomingActivities'));
    }

    public function show(Request $request, Activity $activity): View
    {
        abort_unless($request->user()->hasPermission('activities.view'), 403);

        $member = $request->user()->member;

        if ($activity->isHajatan() && $member) {
            $activity->load([
                'shifts' => fn ($q) => $q
                    ->whereHas('assignments', fn ($a) => $a->where('member_id', $member->id))
                    ->orderBy('urutan')
                    ->orderBy('mulai_pada'),
            ]);
        } else {
            $activity->load(['shifts' => fn ($q) => $q->orderBy('urutan')->orderBy('mulai_pada')]);
        }

        $attendance = $member
            ? $activity->attendances()->where('member_id', $member->id)->first()
            : null;

        /** @var Collection<int, ActivityShiftAttendance> $shiftAttendances */
        $shiftAttendances = collect();
        if ($member && $activity->isHajatan() && $activity->shifts->isNotEmpty()) {
            $shiftAttendances = ActivityShiftAttendance::query()
                ->where('member_id', $member->id)
                ->whereIn('activity_shift_id', $activity->shifts->modelKeys())
                ->get()
                ->keyBy('activity_shift_id');
        }

        return view('member.activities.show', compact('activity', 'attendance', 'shiftAttendances'));
    }

    public function storeAttendance(StoreActivityAttendanceRequest $request, Activity $activity): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('activities.view'), 403);
        abort_if($activity->isHajatan(), 404);
        abort_unless($activity->isInAttendanceWindow(), 403, 'Absensi hanya aktif saat kegiatan berlangsung.');

        $member = $request->user()->member;
        abort_if(! $member, 403, 'Akun belum terhubung ke data anggota.');

        $validated = $request->validated();
        $status = AttendanceStatus::from($validated['status']);

        ActivityAttendance::updateOrCreate(
            [
                'activity_id' => $activity->id,
                'member_id' => $member->id,
            ],
            [
                'status' => $status,
                'keterangan' => $validated['keterangan'] ?? null,
                'recorded_by' => null,
            ]
        );

        return redirect()
            ->route('member.activities.show', $activity)
            ->with('success', $status === AttendanceStatus::Hadir ? 'Absensi berhasil disimpan.' : 'Izin berhasil dikirim.');
    }
}
