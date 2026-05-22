<?php

$t = 'd'.'iv';
$o = '<'.$t;
$cl = '</'.$t.'>';

$content = str_replace(['__O__', '__C__'], [$o, $cl], <<<'BLADE'
@extends('layouts.member')
@section('title', 'Beranda')
@section('content')
__O__ class="mb-5">
<h1 class="text-xl font-bold text-slate-900 sm:text-2xl">Selamat datang, {{ auth()->user()->name }}</h1>
<p class="text-sm text-slate-600">Portal anggota Karang Taruna</p>
__C__
@if ($member)
__O__ class="mb-5 grid grid-cols-2 gap-3">
__O__ class="card p-4"><p class="text-xs text-slate-500">RT</p><p class="font-semibold">{{ $member->rt->label() }}</p>__C__
__O__ class="card p-4"><p class="text-xs text-slate-500">Status</p><p class="font-semibold text-emerald-600">{{ $member->status->label() }}</p>__C__
__C__
@endif
__O__ class="space-y-4">
__O__ class="card p-4">
<h2 class="mb-3 font-semibold">Kegiatan Mendatang</h2>
@forelse ($upcomingActivities as $a)
<a href="{{ route('member.activities.show', $a) }}" class="flex min-h-12 flex-col justify-center border-b border-slate-100 py-3 last:border-0 active:bg-slate-50">
<span class="font-medium text-emerald-700">{{ $a->judul }}</span>
<span class="text-sm text-slate-500">{{ $a->mulai_pada->format('d/m/Y H:i') }}</span>
</a>
@empty<p class="text-sm text-slate-500">Tidak ada jadwal.</p>@endforelse
__C__
__O__ class="card p-4">
<h2 class="mb-3 font-semibold">Pengumuman</h2>
@forelse ($announcements as $an)
<a href="{{ route('member.announcements.show', $an) }}" class="block min-h-11 border-b border-slate-100 py-3 text-sm font-medium text-emerald-700 last:border-0 active:bg-slate-50">{{ $an->judul }}</a>
@empty<p class="text-sm text-slate-500">Tidak ada pengumuman.</p>@endforelse
__C__
__C__
@if ($unpaidDues->isNotEmpty())
__O__ class="card mt-4 border-amber-200 bg-amber-50 p-4">
<h2 class="mb-2 font-semibold text-amber-900">Iuran Belum Lunas</h2>
<ul class="space-y-2 text-sm text-amber-900">@foreach ($unpaidDues as $d)
<li>{{ $d->period->judul }} - @rupiah($d->period->jumlah)</li>@endforeach</ul>
<a href="{{ route('member.dues.index') }}" class="btn-primary mt-3 w-full">Lihat semua iuran</a>
__C__
@endif
@endsection
BLADE);

file_put_contents(dirname(__DIR__).'/resources/views/member/dashboard.blade.php', $content);
echo "ok\n";
