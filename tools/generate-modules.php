<?php

$base = dirname(__DIR__).'/resources/views';
$t = 'd'.'iv';
$open = '<'.$t;
$close = '</'.$t.'>';

function c(string $s): string
{
    $t = 'd'.'iv';

    $s = str_replace('__/D__', '</'.$t.'>', $s);

    return str_replace('__D__', '<'.$t, $s);
}

$views = [];

$views['admin/dashboard.blade.php'] = c(<<<'BLADE'
@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
__D__ class="mb-8"><h1 class="text-2xl font-bold">Dashboard Pengurus</h1><p class="text-slate-600">{{ $organization?->wilayahLabel() }}</p></__/D__
__D__ class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
__D__ class="rounded-xl border bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Anggota Aktif</p><p class="text-3xl font-bold text-emerald-600">{{ $stats['anggota_aktif'] }}</p></__/D__
__D__ class="rounded-xl border bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Saldo Kas</p><p class="text-2xl font-bold">@rupiah($stats['saldo'])</p></__/D__
__D__ class="rounded-xl border bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Kegiatan Mendatang</p><p class="text-3xl font-bold">{{ $stats['kegiatan_aktif'] }}</p></__/D__
__D__ class="rounded-xl border bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Pengumuman</p><p class="text-3xl font-bold">{{ $stats['pengumuman'] }}</p></__/D__
__D__ class="rounded-xl border bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Periode Iuran</p><p class="text-3xl font-bold">{{ $stats['iuran_aktif'] }}</p></__/D__
__/D__
@endsection
BLADE);

$views['admin/activities/index.blade.php'] = c(<<<'BLADE'
@extends('layouts.admin')
@section('title', 'Kegiatan')
@section('content')
__D__ class="mb-6 flex justify-between items-center"><__D__><h1 class="text-2xl font-bold">Kegiatan</h1></__/D__
@if(auth()->user()->hasPermission('activities.manage'))<a href="{{ route('admin.activities.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">+ Tambah</a>@endif</__/D__
__D__ class="mb-4 rounded-xl border bg-white p-4"><form method="GET" class="flex gap-2 flex-wrap">
<input name="q" value="{{ request('q') }}" placeholder="Cari..." class="rounded border px-3 py-2 text-sm">
<select name="status" class="rounded border px-3 py-2 text-sm"><option value="">Semua status</option>@foreach(\App\Enums\ActivityStatus::cases() as $s)<option value="{{ $s->value }}" @selected(request('status')==$s->value)>{{ $s->label() }}</option>@endforeach</select>
<button class="rounded bg-slate-800 px-4 py-2 text-sm text-white">Filter</button></form></__/D__
__D__ class="rounded-xl border bg-white overflow-hidden"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-2 text-left">Judul</th><th class="px-4 py-2 text-left">Waktu</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Hadir</th><th class="px-4 py-2"></th></tr></thead><tbody>
@forelse($activities as $a)<tr class="border-t"><td class="px-4 py-2 font-medium">{{ $a->judul }}</td><td class="px-4 py-2">{{ $a->mulai_pada->format('d/m/Y H:i') }}</td><td class="px-4 py-2">{{ $a->status->label() }}</td><td class="px-4 py-2 text-center">{{ $a->hadir_count ?? 0 }}</td><td class="px-4 py-2 text-right"><a href="{{ route('admin.activities.show',$a) }}" class="text-emerald-600">Detail</a></td></tr>@empty<tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada kegiatan</td></tr>@endforelse
</tbody></table><__D__ class="p-3">{{ $activities->links() }}</__/D__></__/D__
@endsection
BLADE);

$views['admin/activities/_form.blade.php'] = c(<<<'BLADE'
__D__ class="grid gap-4 sm:grid-cols-2">
__D__><label class="text-sm font-medium">Judul *</label><input name="judul" value="{{ old('judul',$activity->judul) }}" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
__D__><label class="text-sm font-medium">Status</label><select name="status" class="w-full rounded border px-3 py-2 text-sm">@foreach(\App\Enums\ActivityStatus::cases() as $s)<option value="{{ $s->value }}" @selected(old('status',$activity->status?->value)==$s->value)>{{ $s->label() }}</option>@endforeach</select></__/D__
__D__><label class="text-sm font-medium">Mulai *</label><input type="datetime-local" name="mulai_pada" value="{{ old('mulai_pada',$activity->mulai_pada?->format('Y-m-d\TH:i')) }}" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
__D__><label class="text-sm font-medium">Selesai</label><input type="datetime-local" name="selesai_pada" value="{{ old('selesai_pada',$activity->selesai_pada?->format('Y-m-d\TH:i')) }}" class="w-full rounded border px-3 py-2 text-sm"></__/D__
__D__><label class="text-sm font-medium">Lokasi</label><input name="lokasi" value="{{ old('lokasi',$activity->lokasi) }}" class="w-full rounded border px-3 py-2 text-sm"></__/D__
__/D__
__D__><label class="text-sm font-medium">Deskripsi</label><textarea name="deskripsi" rows="4" class="w-full rounded border px-3 py-2 text-sm">{{ old('deskripsi',$activity->deskripsi) }}</textarea></__/D__
BLADE);

$views['admin/activities/create.blade.php'] = c(<<<'BLADE'
@extends('layouts.admin')
@section('title','Tambah Kegiatan')
@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Kegiatan</h1>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.activities.store') }}">@csrf @include('admin.activities._form')
__D__ class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-white text-sm">Simpan</button><a href="{{ route('admin.activities.index') }}" class="rounded border px-4 py-2 text-sm">Batal</a></__/D__</form></__/D__
@endsection
BLADE);

$views['admin/activities/edit.blade.php'] = c(<<<'BLADE'
@extends('layouts.admin')
@section('title','Ubah Kegiatan')
@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Kegiatan</h1>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.activities.update',$activity) }}">@csrf @method('PUT') @include('admin.activities._form')
__D__ class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-white text-sm">Simpan</button><a href="{{ route('admin.activities.show',$activity) }}" class="rounded border px-4 py-2 text-sm">Batal</a></__/D__</form></__/D__
@endsection
BLADE);

$views['admin/activities/show.blade.php'] = c(<<<'BLADE'
@extends('layouts.admin')
@section('title',$activity->judul)
@section('content')
__D__ class="mb-6 flex justify-between"><__D__><a href="{{ route('admin.activities.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a><h1 class="text-2xl font-bold mt-1">{{ $activity->judul }}</h1><p class="text-slate-600">{{ $activity->mulai_pada->format('d F Y, H:i') }} @if($activity->lokasi)| {{ $activity->lokasi }}@endif</p></__/D__
@if(auth()->user()->hasPermission('activities.manage'))<__D__ class="flex gap-2"><a href="{{ route('admin.activities.attendance',$activity) }}" class="rounded border px-3 py-2 text-sm">Absensi</a><a href="{{ route('admin.activities.edit',$activity) }}" class="rounded border px-3 py-2 text-sm">Ubah</a>
<form method="POST" action="{{ route('admin.activities.destroy',$activity) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="rounded border border-red-200 px-3 py-2 text-sm text-red-600">Hapus</button></form></__/D__@endif</__/D__
__D__ class="rounded-xl border bg-white p-6 mb-4"><p class="text-sm"><strong>Status:</strong> {{ $activity->status->label() }}</p>@if($activity->deskripsi)<p class="mt-2 text-slate-700">{{ $activity->deskripsi }}</p>@endif</__/D__
__D__ class="rounded-xl border bg-white p-6"><h2 class="font-semibold mb-3">Rekap Absensi ({{ $activity->attendances->where('status','hadir')->count() }} hadir)</h2>
<table class="min-w-full text-sm"><thead><tr class="border-b"><th class="py-2 text-left">Nama</th><th>RT</th><th>Status</th></tr></thead><tbody>
@forelse($activity->attendances as $att)<tr class="border-b"><td class="py-2">{{ $att->member->nama_lengkap }}</td><td>{{ $att->member->rt->label() }}</td><td>{{ $att->status->label() }}</td></tr>@empty<tr><td colspan="3" class="py-4 text-slate-500">Belum ada absensi</td></tr>@endforelse
</tbody></table></__/D__
@endsection
BLADE);

$views['admin/activities/attendance.blade.php'] = c(<<<'BLADE'
@extends('layouts.admin')
@section('title','Absensi')
@section('content')
<h1 class="text-2xl font-bold mb-1">Absensi: {{ $activity->judul }}</h1>
<p class="text-sm text-slate-600 mb-4">{{ $activity->mulai_pada->format('d F Y H:i') }}</p>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.activities.attendance.update',$activity) }}">@csrf
<table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-3 py-2 text-left">Nama</th><th>RT</th><th>Status</th><th>Keterangan</th></tr></thead><tbody>
@foreach($members as $m)@php $att=$existing->get($m->id);@endphp<tr class="border-t"><td class="px-3 py-2">{{ $m->nama_lengkap }}</td><td class="px-3 py-2">{{ $m->rt->label() }}</td>
<td class="px-3 py-2"><select name="attendance[{{ $m->id }}][status]" class="rounded border text-sm">@foreach(\App\Enums\AttendanceStatus::cases() as $s)<option value="{{ $s->value }}" @selected(old("attendance.{$m->id}.status",$att?->status?->value)==$s->value)>{{ $s->label() }}</option>@endforeach</select></td>
<td class="px-3 py-2"><input name="attendance[{{ $m->id }}][keterangan]" value="{{ old("attendance.{$m->id}.keterangan",$att?->keterangan) }}" class="w-full rounded border text-sm"></td></tr>@endforeach
</tbody></table>
__D__ class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-white text-sm">Simpan Absensi</button><a href="{{ route('admin.activities.show',$activity) }}" class="rounded border px-4 py-2 text-sm">Kembali</a></__/D__</form></__/D__
@endsection
BLADE);

foreach ($views as $path => $content) {
    $full = $base.'/'.$path;
    if (! is_dir(dirname($full))) {
        mkdir(dirname($full), 0755, true);
    }
    file_put_contents($full, $content);
}

echo 'Generated '.count($views).' activity views'.PHP_EOL;
