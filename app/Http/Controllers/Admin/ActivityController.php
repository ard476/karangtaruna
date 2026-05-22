<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ActivityKind;
use App\Enums\AttendanceStatus;
use App\Enums\MemberStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Http\Requests\Admin\UpdateAttendanceRequest;
use App\Http\Requests\Admin\UpdateShiftAssignmentsRequest;
use App\Http\Requests\Admin\UpdateShiftAttendanceRequest;
use App\Models\Activity;
use App\Models\ActivityAttendance;
use App\Models\ActivityShift;
use App\Models\ActivityShiftAssignment;
use App\Models\ActivityShiftAttendance;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('activities.view'), 403);

        $activities = Activity::query()
            ->withCount([
                'attendances as absensi_biasa_hadir' => fn ($q) => $q->where('status', AttendanceStatus::Hadir),
                'shiftAttendances as absensi_shift_hadir' => fn ($q) => $q->where('status', AttendanceStatus::Hadir),
            ])
            ->filter($request->only(['status', 'q']))
            ->orderByDesc('mulai_pada')
            ->paginate(12)
            ->withQueryString();

        return view('admin.activities.index', compact('activities'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasPermission('activities.manage'), 403);

        return view('admin.activities.create', [
            'activity' => new Activity(['tipe' => ActivityKind::Biasa]),
        ]);
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $shifts = $validated['shifts'] ?? [];
        unset($validated['shifts']);

        $activity = DB::transaction(function () use ($validated, $shifts, $request) {
            $activity = Activity::create([
                ...$validated,
                'created_by' => $request->user()->id,
            ]);
            if ($activity->isHajatan()) {
                $this->syncShifts($activity, $shifts);
            }

            return $activity;
        });

        return redirect()->route('admin.activities.show', $activity)
            ->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Request $request, Activity $activity): View
    {
        abort_unless($request->user()->hasPermission('activities.view'), 403);

        $activity->load([
            'creator',
            'attendances.member.rt',
            'shifts' => fn ($q) => $q->orderBy('urutan')->orderBy('mulai_pada'),
            'shifts.attendances.member.rt',
        ]);

        return view('admin.activities.show', compact('activity'));
    }

    public function edit(Request $request, Activity $activity): View
    {
        abort_unless($request->user()->hasPermission('activities.manage'), 403);

        $activity->load('shifts');

        return view('admin.activities.edit', compact('activity'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validated();
        $shifts = $validated['shifts'] ?? [];
        unset($validated['shifts']);

        DB::transaction(function () use ($activity, $validated, $shifts) {
            $activity->update($validated);
            if ($activity->isHajatan()) {
                $this->syncShifts($activity, $shifts);
            } else {
                $activity->shifts()->delete();
            }
        });

        return redirect()->route('admin.activities.show', $activity)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Request $request, Activity $activity): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('activities.manage'), 403);

        $activity->delete();

        return redirect()->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function attendance(Request $request, Activity $activity): View|RedirectResponse
    {
        abort_unless($request->user()->hasPermission('activities.manage'), 403);

        if ($activity->isHajatan()) {
            return redirect()
                ->route('admin.activities.show', $activity)
                ->with('error', 'Kegiatan hajatan memakai absensi per shift. Pilih shift di bawah.');
        }

        $members = Member::with('rt')
            ->where('status', MemberStatus::Aktif)
            ->orderBy('nama_lengkap')
            ->get();

        $existing = $activity->attendances()->get()->keyBy('member_id');

        return view('admin.activities.attendance', compact('activity', 'members', 'existing'));
    }

    public function shiftAssignments(Request $request, Activity $activity, ActivityShift $shift): View|RedirectResponse
    {
        abort_unless($request->user()->hasPermission('activities.manage'), 403);
        abort_unless($activity->isHajatan(), 404);
        abort_unless($shift->activity_id === $activity->id, 404);

        $members = Member::with('rt')
            ->where('status', MemberStatus::Aktif)
            ->orderBy('nama_lengkap')
            ->get();

        $assignedIds = $shift->assignments()->pluck('member_id')->all();

        return view('admin.activities.shift-assignments', compact('activity', 'shift', 'members', 'assignedIds'));
    }

    public function updateShiftAssignments(UpdateShiftAssignmentsRequest $request, Activity $activity, ActivityShift $shift): RedirectResponse
    {
        abort_unless($activity->isHajatan(), 404);
        abort_unless($shift->activity_id === $activity->id, 404);

        $memberIds = array_values(array_unique(array_map('intval', $request->validated('member_ids') ?? [])));

        DB::transaction(function () use ($shift, $memberIds) {
            $shift->assignments()->whereNotIn('member_id', $memberIds)->delete();

            foreach ($memberIds as $memberId) {
                ActivityShiftAssignment::firstOrCreate([
                    'activity_shift_id' => $shift->id,
                    'member_id' => $memberId,
                ]);
            }
        });

        return redirect()
            ->route('admin.activities.show', $activity)
            ->with('success', 'Petugas shift "'.$shift->nama.'" berhasil disimpan.');
    }

    public function attendanceShift(Request $request, Activity $activity, ActivityShift $shift): View|RedirectResponse
    {
        abort_unless($request->user()->hasPermission('activities.manage'), 403);
        abort_unless($activity->isHajatan(), 404);
        abort_unless($shift->activity_id === $activity->id, 404);

        if (! $shift->hasAssignments()) {
            return redirect()
                ->route('admin.activities.shift.assignments', [$activity, $shift])
                ->with('error', 'Tambahkan petugas shift terlebih dahulu.');
        }

        $members = $shift->assignedMembers()->with('rt')->orderBy('nama_lengkap')->get();

        $existing = $shift->attendances()->with('member')->get()->keyBy('member_id');

        return view('admin.activities.attendance-shift', compact('activity', 'shift', 'members', 'existing'));
    }

    public function updateShiftAttendance(UpdateShiftAttendanceRequest $request, Activity $activity, ActivityShift $shift): RedirectResponse
    {
        abort_unless($activity->isHajatan(), 404);
        abort_unless($shift->activity_id === $activity->id, 404);

        $disk = Storage::disk('public');

        $allowedMemberIds = $shift->assignments()->pluck('member_id')->all();

        foreach ($request->validated('attendance') as $memberId => $row) {
            if (! in_array((int) $memberId, $allowedMemberIds, true)) {
                continue;
            }

            $status = AttendanceStatus::from($row['status']);

            $existing = ActivityShiftAttendance::query()
                ->where('activity_shift_id', $shift->id)
                ->where('member_id', $memberId)
                ->first();

            $photoPath = $existing?->photo_path;

            if ($request->hasFile("attendance.{$memberId}.photo")) {
                if ($photoPath) {
                    $disk->delete($photoPath);
                }
                $photoPath = $request->file("attendance.{$memberId}.photo")
                    ->store("absensi-hajatan/{$activity->id}/{$shift->id}", 'public');
            }

            if ($status === AttendanceStatus::Hadir && ! $photoPath) {
                return back()
                    ->withInput()
                    ->withErrors(["attendance.{$memberId}.photo" => 'Foto wajib untuk status hadir.']);
            }

            if ($status !== AttendanceStatus::Hadir && $photoPath) {
                $disk->delete($photoPath);
                $photoPath = null;
            }

            ActivityShiftAttendance::updateOrCreate(
                ['activity_shift_id' => $shift->id, 'member_id' => $memberId],
                [
                    'status' => $status,
                    'keterangan' => $row['keterangan'] ?? null,
                    'photo_path' => $photoPath,
                    'absen_pada' => $status === AttendanceStatus::Hadir
                        ? ($existing?->absen_pada ?? now())
                        : null,
                    'recorded_by' => $request->user()->id,
                ]
            );
        }

        return redirect()->route('admin.activities.show', $activity)
            ->with('success', 'Absensi shift berhasil disimpan.');
    }

    public function updateAttendance(UpdateAttendanceRequest $request, Activity $activity): RedirectResponse
    {
        if ($activity->isHajatan()) {
            return redirect()
                ->route('admin.activities.show', $activity)
                ->with('error', 'Gunakan absensi per shift untuk kegiatan hajatan.');
        }

        DB::transaction(function () use ($request, $activity) {
            foreach ($request->validated('attendance') as $memberId => $row) {
                ActivityAttendance::updateOrCreate(
                    ['activity_id' => $activity->id, 'member_id' => $memberId],
                    [
                        'status' => $row['status'],
                        'keterangan' => $row['keterangan'] ?? null,
                        'recorded_by' => $request->user()->id,
                    ]
                );
            }
        });

        return redirect()->route('admin.activities.show', $activity)
            ->with('success', 'Absensi berhasil disimpan.');
    }

    /**
     * @param  array<int, array<string, mixed>>  $shifts
     */
    private function syncShifts(Activity $activity, array $shifts): void
    {
        $keepIds = [];

        foreach (array_values($shifts) as $index => $row) {
            $attrs = [
                'nama' => $row['nama'],
                'urutan' => $index,
                'mulai_pada' => $row['mulai_pada'],
                'selesai_pada' => $row['selesai_pada'] ?? null,
                'lokasi' => $row['lokasi'] ?? null,
                'latitude' => $row['latitude'] ?? null,
                'longitude' => $row['longitude'] ?? null,
                'radius_meters' => $row['radius_meters'] ?? null,
                'catatan' => $row['catatan'] ?? null,
            ];

            if (! empty($row['id'])) {
                $shift = ActivityShift::query()
                    ->where('activity_id', $activity->id)
                    ->where('id', $row['id'])
                    ->first();

                if ($shift) {
                    $shift->update($attrs);
                    $keepIds[] = $shift->id;

                    continue;
                }
            }

            $new = $activity->shifts()->create($attrs);
            $keepIds[] = $new->id;
        }

        $activity->shifts()->whereNotIn('id', $keepIds)->delete();
    }
}
