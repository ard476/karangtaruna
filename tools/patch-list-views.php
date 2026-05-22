<?php

$t = 'd'.'iv';
$o = '<'.$t;
$cl = '</'.$t.'>';
$r = fn (string $s) => str_replace(['__O__', '__C__'], [$o, $cl], $s);

$activities = $r(<<<'BLADE'
@extends('layouts.admin')
@section('title', 'Kegiatan')
@section('content')
<x-page-header title="Kegiatan" subtitle="Jadwal & absensi">
<x-slot:actions>
@if(auth()->user()->hasPermission('activities.manage'))
<a href="{{ route('admin.activities.create') }}" class="btn-primary w-full sm:w-auto">+ Tambah</a>
@endif
</x-slot:actions>
</x-page-header>
__O__ class="card mb-4 p-4"><form method="GET" class="space-y-3">
<input name="q" value="{{ request('q') }}" placeholder="Cari kegiatan..." class="input-touch">
<select name="status" class="select-touch"><option value="">Semua status</option>
@foreach(\App\Enums\ActivityStatus::cases() as $s)<option value="{{ $s->value }}" @selected(request('status')==$s->value)>{{ $s->label() }}</option>@endforeach</select>
<button class="btn-primary w-full">Filter</button>
</form></__C__
__O__ class="space-y-3 md:hidden">
@forelse($activities as $a)
<article class="card p-4">
<h3 class="font-semibold text-slate-900">{{ $a->judul }}</h3>
<p class="mt-1 text-sm text-slate-600">{{ $a->mulai_pada->format('d/m/Y H:i') }}</p>
<p class="text-xs text-slate-500">{{ $a->status->label() }} · {{ $a->hadir_count ?? 0 }} hadir</p>
<a href="{{ route('admin.activities.show',$a) }}" class="btn-secondary mt-3 w-full justify-center py-2 text-xs">Detail</a>
</article>
@empty<p class="card p-6 text-center text-slate-500">Belum ada kegiatan</p>@endforelse
</__C__
__O__ class="card hidden md:block"><__O__ class="kt-table-wrap">
<table class="kt-table-responsive min-w-full text-sm"><thead class="bg-slate-50"><tr>
<th class="px-4 py-2 text-left">Judul</th><th class="px-4 py-2 text-left">Waktu</th><th>Status</th><th>Hadir</th><th></th>
</tr></thead><tbody>
@forelse($activities as $a)<tr class="border-t">
<td data-label="Judul" class="px-4 py-2 font-medium">{{ $a->judul }}</td>
<td data-label="Waktu" class="px-4 py-2">{{ $a->mulai_pada->format('d/m/Y H:i') }}</td>
<td data-label="Status" class="px-4 py-2">{{ $a->status->label() }}</td>
<td data-label="Hadir" class="px-4 py-2 text-center">{{ $a->hadir_count ?? 0 }}</td>
<td data-label="" class="kt-actions px-4 py-2 text-right"><a href="{{ route('admin.activities.show',$a) }}" class="text-emerald-600">Detail</a></td>
</tr>@empty<tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada kegiatan</td></tr>@endforelse
</tbody></table></__C__></__C__
@if($activities->hasPages())__O__ class="mt-4">{{ $activities->links() }}</__C__@endif
@endsection
BLADE);

file_put_contents(dirname(__DIR__).'/resources/views/admin/activities/index.blade.php', $activities);

$transactions = file_get_contents(dirname(__DIR__).'/resources/views/admin/transactions/index.blade.php');
$transactions = str_replace('rounded-lg bg-emerald-600', 'btn-primary', $transactions);
$transactions = str_replace('rounded bg-slate-800 px-4 py-2 text-sm text-white', 'btn-primary w-full sm:w-auto', $transactions);
$transactions = str_replace('rounded border px-3 py-2 text-sm', 'input-touch', $transactions);
$transactions = preg_replace('/<table class="min-w-full/', '<div class="kt-table-wrap"><table class="kt-table-responsive min-w-full', $transactions, 1);
$transactions = preg_replace('/<\/table>/', '</table></div>', $transactions, 1);
file_put_contents(dirname(__DIR__).'/resources/views/admin/transactions/index.blade.php', $transactions);

echo "patched\n";
