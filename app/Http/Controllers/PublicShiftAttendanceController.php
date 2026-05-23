<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Http\Requests\PublicStoreShiftAttendanceRequest;
use App\Models\ActivityShift;
use App\Models\ActivityShiftAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicShiftAttendanceController extends Controller
{
    public function create(ActivityShift $shift): View
    {
        abort_if(! $shift->qr_token, 404);

        $shift->load('activity');

        abort_unless($shift->activity->isHajatan(), 404);

        return view('public.shift-attendance', [
            'activity' => $shift->activity,
            'shift' => $shift,
        ]);
    }

    public function store(PublicStoreShiftAttendanceRequest $request, ActivityShift $shift): RedirectResponse
    {
        abort_if(! $shift->qr_token, 404);

        $shift->load('activity');
        abort_unless($shift->activity->isHajatan(), 404);
        abort_unless($shift->isInAttendanceWindow(), 403, 'Absensi hanya aktif saat shift berlangsung.');

        $validated = $request->validated();
        $publicName = $validated['nama_lengkap'];

        $distanceMeters = null;
        $latitude = isset($validated['latitude']) ? (float) $validated['latitude'] : null;
        $longitude = isset($validated['longitude']) ? (float) $validated['longitude'] : null;

        if ($shift->hasRadius() && $latitude !== null && $longitude !== null) {
            $distanceMeters = $shift->distanceTo($latitude, $longitude);
        }

        $disk = Storage::disk('public');
        $existing = ActivityShiftAttendance::query()
            ->where('activity_shift_id', $shift->id)
            ->whereNull('member_id')
            ->whereRaw('LOWER(public_name) = ?', [mb_strtolower($publicName)])
            ->first();

        if ($existing?->photo_path) {
            $disk->delete($existing->photo_path);
        }

        $path = $request->file('photo')
            ->store("absensi-hajatan/{$shift->activity_id}/{$shift->id}", 'public');

        ActivityShiftAttendance::updateOrCreate(
            [
                'activity_shift_id' => $shift->id,
                'member_id' => null,
                'public_name' => $existing?->public_name ?? $publicName,
            ],
            [
                'status' => AttendanceStatus::Hadir,
                'public_name' => $publicName,
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
            ->route('public.shift-attendance.create', $shift->qr_token)
            ->with('success', 'Absensi berhasil dikirim.');
    }
}
