<?php

namespace App\Http\Controllers\Member;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreShiftAttendanceRequest;
use App\Models\Activity;
use App\Models\ActivityShift;
use App\Models\ActivityShiftAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ActivityShiftAttendanceController extends Controller
{
    public function create(Request $request, Activity $activity, ActivityShift $shift): View
    {
        abort_unless($request->user()->hasPermission('activities.view'), 403);
        abort_unless($activity->isHajatan(), 404);
        abort_unless($shift->activity_id === $activity->id, 404);

        $member = $request->user()->member;
        abort_if(! $member, 403, 'Akun belum terhubung ke data anggota.');
        abort_unless($shift->isAssignedTo($member), 403, 'Anda tidak ditugaskan pada shift ini.');

        if (! $shift->isInAttendanceWindow()) {
            abort(403, 'Absensi hanya aktif saat shift berlangsung.');
        }

        $attendance = ActivityShiftAttendance::query()
            ->where('activity_shift_id', $shift->id)
            ->where('member_id', $member->id)
            ->first();

        return view('member.activities.shift-absen', compact('activity', 'shift', 'member', 'attendance'));
    }

    public function store(StoreShiftAttendanceRequest $request, Activity $activity, ActivityShift $shift): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('activities.view'), 403);
        abort_unless($activity->isHajatan(), 404);
        abort_unless($shift->activity_id === $activity->id, 404);

        $member = $request->user()->member;
        abort_if(! $member, 403);
        abort_unless($shift->isAssignedTo($member), 403, 'Anda tidak ditugaskan pada shift ini.');
        abort_unless($shift->isInAttendanceWindow(), 403, 'Absensi hanya aktif saat shift berlangsung.');

        $validated = $request->validated();
        $status = AttendanceStatus::from($validated['status']);

        if ($status === AttendanceStatus::Izin) {
            $existing = ActivityShiftAttendance::query()
                ->where('activity_shift_id', $shift->id)
                ->where('member_id', $member->id)
                ->first();

            if ($existing?->photo_path) {
                Storage::disk('public')->delete($existing->photo_path);
            }

            ActivityShiftAttendance::updateOrCreate(
                [
                    'activity_shift_id' => $shift->id,
                    'member_id' => $member->id,
                ],
                [
                    'status' => AttendanceStatus::Izin,
                    'photo_path' => null,
                    'absen_latitude' => null,
                    'absen_longitude' => null,
                    'distance_meters' => null,
                    'keterangan' => $validated['keterangan'] ?? null,
                    'absen_pada' => null,
                    'recorded_by' => null,
                ]
            );

            return redirect()
                ->route('member.activities.show', $activity)
                ->with('success', 'Izin shift berhasil dikirim.');
        }

        $distanceMeters = null;
        $latitude = isset($validated['latitude']) ? (float) $validated['latitude'] : null;
        $longitude = isset($validated['longitude']) ? (float) $validated['longitude'] : null;

        if ($shift->hasRadius() && $latitude !== null && $longitude !== null) {
            $distanceMeters = $shift->distanceTo($latitude, $longitude);
        }

        $disk = Storage::disk('public');

        $existing = ActivityShiftAttendance::query()
            ->where('activity_shift_id', $shift->id)
            ->where('member_id', $member->id)
            ->first();

        if ($existing?->photo_path) {
            $disk->delete($existing->photo_path);
        }

        $path = $request->file('photo')->store("absensi-hajatan/{$activity->id}/{$shift->id}", 'public');

        ActivityShiftAttendance::updateOrCreate(
            [
                'activity_shift_id' => $shift->id,
                'member_id' => $member->id,
            ],
            [
                'status' => AttendanceStatus::Hadir,
                'photo_path' => $path,
                'absen_latitude' => $latitude,
                'absen_longitude' => $longitude,
                'distance_meters' => $distanceMeters,
                'keterangan' => $validated['keterangan'] ?? null,
                'absen_pada' => now(),
                'recorded_by' => null,
            ]
        );

        return redirect()
            ->route('member.activities.show', $activity)
            ->with('success', 'Absensi shift berhasil dikirim (dengan foto).');
    }
}
