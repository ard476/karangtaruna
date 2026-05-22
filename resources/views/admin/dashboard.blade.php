@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
@if(auth()->user()->isSuperAdmin())
<div class="mb-4 rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm text-violet-900">
    Anda login sebagai <strong>Super Admin</strong> — akses penuh ke semua modul.
</div>
@endif
<div class="mb-8"><h1 class="text-2xl font-bold">Dashboard Pengurus</h1><p class="text-slate-600">{{ $organization?->wilayahLabel() }}</p></div>
<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3">
<div class="card p-4 sm:p-6"><p class="text-sm text-slate-500">Anggota Aktif</p><p class="text-3xl font-bold text-emerald-600">{{ $stats['anggota_aktif'] }}</p></div>
<div class="card p-4 sm:p-6"><p class="text-sm text-slate-500">Saldo Kas</p><p class="text-2xl font-bold">@rupiah($stats['saldo'])</p></div>
<div class="card p-4 sm:p-6"><p class="text-sm text-slate-500">Kegiatan Mendatang</p><p class="text-3xl font-bold">{{ $stats['kegiatan_aktif'] }}</p></div>
<div class="card p-4 sm:p-6"><p class="text-sm text-slate-500">Pengumuman</p><p class="text-3xl font-bold">{{ $stats['pengumuman'] }}</p></div>
<div class="card p-4 sm:p-6"><p class="text-sm text-slate-500">Periode Iuran</p><p class="text-3xl font-bold">{{ $stats['iuran_aktif'] }}</p></div>
</div>
@endsection