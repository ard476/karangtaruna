@extends('layouts.admin')
@section('title', 'Absensi Shift')
@section('content')
<a href="{{ route('admin.activities.show', $activity) }}" class="text-sm text-emerald-600">&larr; Kembali ke kegiatan</a>
<h1 class="text-2xl font-bold mt-2">Absensi shift: {{ $shift->nama }}</h1>
<p class="text-sm text-slate-600 mb-1">{{ $activity->judul }}</p>
<p class="text-sm text-slate-500 mb-4">
    {{ $shift->mulai_pada->format('d F Y, H:i') }}
    @if($shift->selesai_pada) &mdash; {{ $shift->selesai_pada->format('d F Y, H:i') }} @endif
    @if($shift->lokasi) &middot; {{ $shift->lokasi }} @endif
</p>
@if($shift->catatan)<p class="text-xs text-slate-500 mb-4">{{ $shift->catatan }}</p>@endif

<div class="rounded-xl border bg-white p-6">
    <form method="POST" action="{{ route('admin.activities.attendance.shift.update', [$activity, $shift]) }}" enctype="multipart/form-data">
        @csrf
        <p class="text-xs text-slate-600 mb-4">Status <strong>Hadir</strong> wajib dilengkapi foto (unggah baru atau gunakan foto yang sudah ada).</p>
        <div class="kt-table-wrap overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Nama</th>
                        <th class="px-3 py-2 text-left">RT</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Foto</th>
                        <th class="px-3 py-2 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $m)
                        @php $att = $existing->get($m->id); @endphp
                        <tr class="border-t align-top">
                            <td class="px-3 py-3 font-medium">{{ $m->nama_lengkap }}</td>
                            <td class="px-3 py-3">{{ $m->rt->label() }}</td>
                            <td class="px-3 py-3">
                                <select name="attendance[{{ $m->id }}][status]" class="rounded border px-2 py-1.5 text-sm w-full max-w-[140px]">
                                    @foreach(\App\Enums\AttendanceStatus::cases() as $s)
                                        <option value="{{ $s->value }}" @selected(old("attendance.{$m->id}.status", $att?->status?->value) === $s->value)>{{ $s->label() }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-3 min-w-[180px]">
                                @if($att?->photo_path)
                                    <a href="{{ $att->photoUrl() }}" target="_blank" rel="noopener" class="block mb-2">
                                        <img src="{{ $att->photoUrl() }}" alt="Foto absen" class="h-16 w-16 rounded border object-cover">
                                    </a>
                                    <p class="text-[10px] text-slate-500 mb-1">Foto tersimpan</p>
                                @endif
                                <input type="file" name="attendance[{{ $m->id }}][photo]" accept="image/*" capture="environment" class="block w-full text-xs">
                                @error("attendance.{$m->id}.photo")
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="px-3 py-3">
                                <input name="attendance[{{ $m->id }}][keterangan]" value="{{ old("attendance.{$m->id}.keterangan", $att?->keterangan) }}" class="w-full rounded border px-2 py-1.5 text-sm">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button type="submit" class="rounded bg-emerald-600 px-4 py-2 text-white text-sm">Simpan absensi shift</button>
            <a href="{{ route('admin.activities.show', $activity) }}" class="rounded border px-4 py-2 text-sm">Batal</a>
        </div>
    </form>
</div>
@endsection
