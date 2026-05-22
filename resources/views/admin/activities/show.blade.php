@extends('layouts.admin')
@php use App\Enums\AttendanceStatus; @endphp
@section('title', $activity->judul)
@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:justify-between">
    <div>
        <a href="{{ route('admin.activities.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a>
        <h1 class="text-2xl font-bold mt-1">{{ $activity->judul }}</h1>
        <p class="text-slate-600">{{ $activity->mulai_pada->format('d F Y, H:i') }} @if($activity->lokasi)| {{ $activity->lokasi }}@endif</p>
        <p class="mt-1 text-sm">
            <span class="rounded-full bg-slate-100 px-2 py-0.5">{{ $activity->status->label() }}</span>
            <span class="rounded-full bg-amber-50 text-amber-800 px-2 py-0.5 ml-1">{{ $activity->tipe?->label() ?? 'Biasa' }}</span>
        </p>
    </div>
    @if(auth()->user()->hasPermission('activities.manage'))
        <div class="flex flex-wrap gap-2">
            @unless($activity->isHajatan())
                <a href="{{ route('admin.activities.attendance', $activity) }}" class="rounded border px-3 py-2 text-sm">Absensi</a>
            @endunless
            <a href="{{ route('admin.activities.edit', $activity) }}" class="rounded border px-3 py-2 text-sm">Ubah</a>
            <form method="POST" action="{{ route('admin.activities.destroy', $activity) }}" onsubmit="return confirm('Hapus kegiatan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded border border-red-200 px-3 py-2 text-sm text-red-600">Hapus</button>
            </form>
        </div>
    @endif
</div>

<div class="rounded-xl border bg-white p-6 mb-4">
    @if($activity->deskripsi)<p class="text-slate-700 whitespace-pre-wrap">{{ $activity->deskripsi }}</p>@else<p class="text-slate-500 text-sm">Tidak ada deskripsi.</p>@endif
</div>

@if($activity->isHajatan())
    <div class="rounded-xl border bg-white p-6 mb-4">
        <h2 class="font-semibold mb-3">Shift &amp; petugas</h2>
        @forelse($activity->shifts as $shift)
            @php
                $petugas = $shift->assignments->count();
                $hadir = $shift->attendances->where('status', AttendanceStatus::Hadir)->count();
                $belum = max(0, $petugas - $hadir);
            @endphp
            <div class="border-b py-4 last:border-0 first:pt-0">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="font-medium">{{ $shift->nama }}</p>
                        <p class="text-sm text-slate-600">
                            {{ $shift->mulai_pada->format('d/m/Y H:i') }}
                            @if($shift->selesai_pada) &mdash; {{ $shift->selesai_pada->format('d/m/Y H:i') }} @endif
                        </p>
                        @if($shift->lokasi)<p class="text-xs text-slate-500">{{ $shift->lokasi }}</p>@endif
                        @if($shift->hasRadius())
                            <p class="text-xs text-amber-700 mt-1">Radius: {{ $shift->radius_meters }} m</p>
                        @endif
                        <p class="text-xs text-slate-500 mt-1">Kode WA: <code class="rounded bg-slate-100 px-1 py-0.5">SFT{{ $shift->id }}</code></p>
                        <p class="text-xs text-slate-600 mt-2">
                            Petugas: <strong>{{ $petugas }}</strong>
                            &middot; Hadir: <strong class="text-emerald-700">{{ $hadir }}</strong>
                            &middot; Belum: <strong class="text-amber-700">{{ $belum }}</strong>
                        </p>
                        @if($shift->qr_token)
                            @php
                                $qrUrl = route('public.shift-attendance.create', $shift->qr_token);
                                $qrImageUrl = route('admin.activities.shift.qr-image', [$activity, $shift]);
                            @endphp
                            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs font-semibold text-slate-700">QR absen tanpa login</p>
                                <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <img src="{{ $qrImageUrl }}" alt="QR absen {{ $shift->nama }}" class="h-28 w-28 rounded-lg border bg-white p-1">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ $qrUrl }}" target="_blank" rel="noopener" class="text-xs font-medium text-emerald-700">Buka halaman absen QR</a>
                                        <input value="{{ $qrUrl }}" readonly onclick="this.select()" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs text-slate-600">
                                        <a
                                            href="{{ route('admin.activities.shift.qr-download', [$activity, $shift]) }}"
                                            class="mt-2 inline-flex w-full items-center justify-center rounded-lg border border-emerald-600 bg-white px-3 py-2 text-xs font-medium text-emerald-700"
                                        >
                                            Download foto QR
                                        </a>
                                        <p class="mt-1 text-[11px] text-slate-500">Link ini statis untuk shift ini. Anggota scan QR, pilih nama, ambil foto, lalu kirim absen.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($petugas === 0)
                            <p class="text-xs text-red-600 mt-1">Belum ada petugas ditugaskan.</p>
                        @endif
                    </div>
                    @if(auth()->user()->hasPermission('activities.manage'))
                        <div class="flex flex-col gap-2 shrink-0">
                            <a href="{{ route('admin.activities.shift.assignments', [$activity, $shift]) }}" class="rounded border border-slate-300 px-3 py-2 text-sm text-center">Atur petugas</a>
                            @if($petugas > 0)
                                <a href="{{ route('admin.activities.attendance.shift', [$activity, $shift]) }}" class="rounded border border-emerald-600 px-3 py-2 text-sm text-emerald-700 text-center">Kelola absensi</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-slate-500 text-sm">Belum ada shift. Ubah kegiatan untuk menambah shift.</p>
        @endforelse
    </div>

    @foreach($activity->shifts as $shift)
        @if($shift->assignments->isNotEmpty() || $shift->attendances->whereNull('member_id')->isNotEmpty())
            @php
                $attByMember = $shift->attendances->keyBy('member_id');
                $publicAttendances = $shift->attendances->whereNull('member_id');
            @endphp
            <div class="rounded-xl border bg-white p-6 mb-4">
                <h3 class="font-semibold text-sm mb-3">Rekap absensi: {{ $shift->nama }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b bg-slate-50">
                                <th class="py-2 text-left px-2">Nama</th>
                                <th class="px-2">RT</th>
                                <th class="px-2">Absensi</th>
                                <th class="px-2">Foto</th>
                                <th class="px-2">Jarak</th>
                                <th class="px-2">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shift->assignments->sortBy(fn ($a) => $a->member->nama_lengkap) as $assignment)
                                @php $att = $attByMember->get($assignment->member_id); @endphp
                                <tr class="border-b">
                                    <td class="py-2 px-2">{{ $assignment->member->nama_lengkap }}</td>
                                    <td class="px-2 text-center">{{ $assignment->member->rt->label() }}</td>
                                    <td class="px-2 text-center">
                                        @if($att?->status === AttendanceStatus::Hadir)
                                            <span class="text-emerald-700">{{ $att->status->label() }}</span>
                                        @else
                                            <span class="text-amber-700">Belum absen</span>
                                        @endif
                                    </td>
                                    <td class="px-2">
                                        @if($att?->photo_path)
                                            <a href="{{ $att->photoUrl() }}" target="_blank" rel="noopener">
                                                <img src="{{ $att->photoUrl() }}" alt="" class="h-10 w-10 rounded object-cover border inline-block">
                                            </a>
                                        @else &mdash; @endif
                                    </td>
                                    <td class="px-2 text-center text-xs text-slate-600">{{ $att?->distance_meters !== null ? $att->distance_meters.' m' : '—' }}</td>
                                    <td class="px-2 text-xs text-slate-600">{{ $att?->absen_pada?->format('d/m/Y H:i') ?? '—' }}</td>
                                </tr>
                            @endforeach
                            @foreach($publicAttendances as $att)
                                <tr class="border-b bg-emerald-50/40">
                                    <td class="py-2 px-2">{{ $att->public_name }}</td>
                                    <td class="px-2 text-center">QR</td>
                                    <td class="px-2 text-center">
                                        <span class="text-emerald-700">{{ $att->status->label() }}</span>
                                    </td>
                                    <td class="px-2">
                                        @if($att->photo_path)
                                            <a href="{{ $att->photoUrl() }}" target="_blank" rel="noopener">
                                                <img src="{{ $att->photoUrl() }}" alt="" class="h-10 w-10 rounded object-cover border inline-block">
                                            </a>
                                        @else &mdash; @endif
                                    </td>
                                    <td class="px-2 text-center text-xs text-slate-600">{{ $att->distance_meters !== null ? $att->distance_meters.' m' : '—' }}</td>
                                    <td class="px-2 text-xs text-slate-600">{{ $att->absen_pada?->format('d/m/Y H:i') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="rounded-xl border bg-white p-6">
        <h2 class="font-semibold mb-3">Rekap absensi ({{ $activity->attendances->where('status', AttendanceStatus::Hadir)->count() }} hadir)</h2>
        <table class="min-w-full text-sm">
            <thead><tr class="border-b"><th class="py-2 text-left">Nama</th><th>RT</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($activity->attendances as $att)
                    <tr class="border-b">
                        <td class="py-2">{{ $att->member->nama_lengkap }}</td>
                        <td>{{ $att->member->rt->label() }}</td>
                        <td>{{ $att->status->label() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-slate-500">Belum ada absensi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
@endsection
