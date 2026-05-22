@extends('layouts.member')
@section('title', 'Beranda')
@section('content')
<div class="mb-5">
<h1 class="text-xl font-bold text-slate-900 sm:text-2xl">Selamat datang, {{ auth()->user()->name }}</h1>
<p class="text-sm text-slate-600">Portal anggota Karang Taruna</p>
</div>
@if ($member)
<div class="mb-5 grid grid-cols-2 gap-3">
<div class="card p-4"><p class="text-xs text-slate-500">RT</p><p class="font-semibold">{{ $member->rt->label() }}</p></div>
<div class="card p-4"><p class="text-xs text-slate-500">Status</p><p class="font-semibold text-emerald-600">{{ $member->status->label() }}</p></div>
</div>
@endif
<div class="space-y-4">
<div class="card p-4">
<h2 class="mb-3 font-semibold">Sedang Berlangsung</h2>
@forelse ($ongoingActivities as $a)
<div class="border-b border-emerald-100 py-3 last:border-0">
<a href="{{ route('member.activities.show', $a) }}" class="block active:bg-emerald-50">
<span class="block font-medium text-emerald-800">{{ $a->judul }}</span>
<span class="block text-sm text-slate-600">{{ $a->mulai_pada->format('d/m/Y H:i') }} @if($a->selesai_pada) - {{ $a->selesai_pada->format('H:i') }} @endif</span>
<span class="block text-xs text-emerald-700">Bisa absen sekarang @if($a->isHajatan()) · Hajatan @endif</span>
</a>
@if(auth()->user()->member)
<div class="mt-2 grid grid-cols-2 gap-2">
@if($a->isHajatan())
<a href="{{ route('member.activities.show', $a) }}" class="btn-primary text-center text-xs py-2">Absen</a>
<a href="{{ route('member.activities.show', $a) }}" class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-center text-xs font-medium text-amber-800">Izin</a>
@else
<form method="POST" action="{{ route('member.activities.attendance.store', $a) }}">@csrf<input type="hidden" name="status" value="hadir"><button type="submit" class="btn-primary w-full py-2 text-xs">Absen</button></form>
<form method="POST" action="{{ route('member.activities.attendance.store', $a) }}">@csrf<input type="hidden" name="status" value="izin"><button type="submit" class="w-full rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800">Izin</button></form>
@endif
</div>
@endif
</div>
@empty<p class="text-sm text-slate-500">Tidak ada kegiatan aktif.</p>@endforelse
</div>
<div class="card p-4">
<h2 class="mb-3 font-semibold">Kegiatan Mendatang</h2>
@forelse ($upcomingActivities as $a)
<div class="border-b border-slate-100 py-3 last:border-0">
<a href="{{ route('member.activities.show', $a) }}" class="block active:bg-slate-50">
<span class="block font-medium text-emerald-700">{{ $a->judul }}</span>
<span class="block text-sm text-slate-500">{{ $a->mulai_pada->format('d/m/Y H:i') }}</span>
</a>
@if(auth()->user()->member)
<div class="mt-2 grid grid-cols-2 gap-2">
<button type="button" disabled class="w-full rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-500">Absen</button>
<button type="button" disabled class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-500">Izin</button>
</div>
<p class="mt-1 text-[11px] text-slate-500">Aktif saat kegiatan/shift berlangsung.</p>
@endif
</div>
@empty<p class="text-sm text-slate-500">Tidak ada jadwal.</p>@endforelse
</div>
<div class="card p-4">
<h2 class="mb-3 font-semibold">Pengumuman</h2>
@forelse ($announcements as $an)
<a href="{{ route('member.announcements.show', $an) }}" class="block min-h-11 border-b border-slate-100 py-3 text-sm font-medium text-emerald-700 last:border-0 active:bg-slate-50">{{ $an->judul }}</a>
@empty<p class="text-sm text-slate-500">Tidak ada pengumuman.</p>@endforelse
</div>
</div>
@if ($unpaidDues->isNotEmpty())
<div class="card mt-4 border-amber-200 bg-amber-50 p-4">
<h2 class="mb-2 font-semibold text-amber-900">Iuran Belum Lunas</h2>
<ul class="space-y-2 text-sm text-amber-900">@foreach ($unpaidDues as $d)
<li>{{ $d->period->judul }} - @rupiah($d->period->jumlah)</li>@endforeach</ul>
<a href="{{ route('member.dues.index') }}" class="btn-primary mt-3 w-full">Lihat semua iuran</a>
</div>
@endif
@endsection