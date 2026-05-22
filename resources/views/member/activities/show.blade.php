@extends('layouts.member')
@php use App\Enums\AttendanceStatus; @endphp
@section('title', $activity->judul)
@section('content')
<a href="{{ route('member.activities.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a>
<h1 class="text-2xl font-bold mt-2">{{ $activity->judul }}</h1>
<p class="text-sm text-slate-600 mb-1">
    {{ $activity->mulai_pada->format('d F Y, H:i') }}
    @if($activity->lokasi) &middot; {{ $activity->lokasi }} @endif
</p>
@if($activity->isHajatan())
    <span class="inline-block rounded-full bg-amber-50 text-amber-800 text-xs px-2 py-0.5 mb-4">Hajatan — absen per shift dengan foto</span>
@endif

<div class="rounded-xl border bg-white p-5 mb-4">
    @if($activity->deskripsi)
        <p class="whitespace-pre-wrap text-sm">{{ $activity->deskripsi }}</p>
    @else
        <p class="text-sm text-slate-500">Tidak ada deskripsi.</p>
    @endif
</div>

@if($activity->isHajatan())
    @if(!auth()->user()->member)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            Akun Anda belum terhubung ke data anggota. Hubungi pengurus untuk absensi shift.
        </div>
    @else
        <h2 class="text-sm font-semibold text-slate-800 mb-3">Shift yang ditugaskan kepada Anda</h2>
        <div class="space-y-3">
            @forelse($activity->shifts as $shift)
                @php
                    $att = $shiftAttendances->get($shift->id);
                    $shiftPhase = $shift->attendanceWindowPhase();
                    $shiftActive = $shiftPhase === 'active';
                @endphp
                <article class="rounded-xl border bg-white p-4">
                    <p class="font-semibold text-slate-900">{{ $shift->nama }}</p>
                    <p class="text-sm text-slate-600 mt-1">
                        {{ $shift->mulai_pada->format('d/m/Y H:i') }}
                        @if($shift->selesai_pada) &mdash; {{ $shift->selesai_pada->format('H:i') }} @endif
                    </p>
                    @if($shift->lokasi)<p class="text-xs text-slate-500 mt-1">{{ $shift->lokasi }}</p>@endif
                    @if($shift->hasRadius())<p class="text-xs text-amber-700 mt-1">Radius absen: {{ $shift->radius_meters }} meter</p>@endif

                    @if($att?->status === AttendanceStatus::Hadir)
                        <div class="mt-3 rounded-lg bg-emerald-50 p-3 text-sm">
                            <p class="text-emerald-800 font-medium">Sudah absen: {{ $att->status->label() }}</p>
                            @if($att->absen_pada)<p class="text-xs text-emerald-700">{{ $att->absen_pada->format('d/m/Y H:i') }}</p>@endif
                            @if($att->distance_meters !== null)<p class="text-xs text-emerald-700">Jarak dari titik absen: {{ $att->distance_meters }} meter</p>@endif
                            @if($att->photo_path)
                                <img src="{{ $att->photoUrl() }}" alt="Foto absen" class="mt-2 h-20 w-20 rounded-lg border object-cover">
                            @endif
                        </div>
                    @elseif($att?->status === AttendanceStatus::Izin)
                        <div class="mt-3 rounded-lg bg-amber-50 p-3 text-sm">
                            <p class="font-medium text-amber-800">Status: {{ $att->status->label() }}</p>
                            @if($att->keterangan)<p class="mt-1 text-xs text-amber-700">{{ $att->keterangan }}</p>@endif
                        </div>
                    @else
                        <p class="text-xs text-slate-500 mt-2">Belum absen shift ini.</p>
                    @endif

                    @if($shiftActive)
                        <a href="{{ route('member.activities.shift-absen', [$activity, $shift]) }}" class="btn-primary mt-3 w-full text-center text-sm py-2.5">
                            {{ $att?->status === AttendanceStatus::Hadir ? 'Perbarui absensi & foto' : 'Absen dengan foto' }}
                        </a>
                        <form method="POST" action="{{ route('member.activities.shift-absen.store', [$activity, $shift]) }}" class="mt-2 space-y-2">
                            @csrf
                            <input type="hidden" name="status" value="izin">
                            <input name="keterangan" value="{{ old('keterangan', $att?->status === AttendanceStatus::Izin ? $att?->keterangan : '') }}" placeholder="Alasan izin (opsional)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <button type="submit" class="w-full rounded-lg border border-amber-300 bg-amber-50 px-3 py-2.5 text-sm font-medium text-amber-800">
                                {{ $att?->status === AttendanceStatus::Izin ? 'Perbarui izin' : 'Izin' }}
                            </button>
                        </form>
                    @elseif($shiftPhase === 'before')
                        <p class="mt-3 rounded-lg bg-slate-100 px-3 py-2.5 text-center text-sm font-medium text-slate-500">
                            Absen/izin dibuka {{ $shift->mulai_pada->format('d/m/Y H:i') }} WIB
                        </p>
                    @else
                        <p class="mt-3 rounded-lg bg-slate-100 px-3 py-2.5 text-center text-sm font-medium text-slate-500">
                            Jendela absen shift berakhir {{ $shift->selesai_pada?->format('d/m/Y H:i') }} WIB
                        </p>
                    @endif
                </article>
            @empty
                <p class="text-sm text-slate-500">Anda belum ditugaskan pada shift manapun untuk kegiatan ini. Hubungi pengurus.</p>
            @endforelse
        </div>
    @endif
@else
    @if(!auth()->user()->member)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            Akun Anda belum terhubung ke data anggota. Hubungi pengurus untuk absensi.
        </div>
    @else
        @if($attendance)
            <div class="rounded-xl border {{ $attendance->status === AttendanceStatus::Hadir ? 'bg-emerald-50' : 'bg-amber-50' }} p-4 text-sm mb-4">
                <strong>Kehadiran Anda:</strong> {{ $attendance->status->label() }}
                @if($attendance->keterangan)<p class="mt-1 text-xs text-slate-600">{{ $attendance->keterangan }}</p>@endif
            </div>
        @endif

        @if($activity->isInAttendanceWindow())
            <div class="rounded-xl border bg-white p-4 space-y-3">
                <form method="POST" action="{{ route('member.activities.attendance.store', $activity) }}">
                    @csrf
                    <input type="hidden" name="status" value="hadir">
                    <button type="submit" class="btn-primary w-full">Absen Hadir</button>
                </form>

                <form method="POST" action="{{ route('member.activities.attendance.store', $activity) }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="status" value="izin">
                    <input name="keterangan" value="{{ old('keterangan', $attendance?->status === AttendanceStatus::Izin ? $attendance?->keterangan : '') }}" placeholder="Alasan izin (opsional)" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <button type="submit" class="w-full rounded-lg border border-amber-300 bg-amber-50 px-3 py-2.5 text-sm font-medium text-amber-800">Izin</button>
                </form>
            </div>
        @else
            <p class="rounded-xl border bg-slate-100 p-4 text-center text-sm font-medium text-slate-500">Tombol absen dan izin aktif saat kegiatan berlangsung.</p>
        @endif
    @endif
@endif
@endsection
