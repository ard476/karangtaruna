@extends('layouts.admin')
@php use App\Enums\ActivityKind; @endphp
@section('title', 'Kegiatan')
@section('content')
<x-page-header title="Kegiatan" subtitle="Jadwal & absensi">
<x-slot:actions>
@if(auth()->user()->hasPermission('activities.manage'))
<a href="{{ route('admin.activities.create') }}" class="btn-primary w-full sm:w-auto">+ Tambah</a>
@endif
</x-slot:actions>
</x-page-header>
<div class="card mb-4 p-4">
    <form method="GET" class="space-y-3">
        <input name="q" value="{{ request('q') }}" placeholder="Cari kegiatan..." class="input-touch">
        <select name="status" class="select-touch">
            <option value="">Semua status</option>
            @foreach(\App\Enums\ActivityStatus::cases() as $s)
                <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary w-full">Filter</button>
    </form>
</div>
<div class="space-y-3 md:hidden">
    @forelse($activities as $a)
        @php
            $hadir = ($a->tipe === ActivityKind::Hajatan)
                ? ($a->absensi_shift_hadir ?? 0)
                : ($a->absensi_biasa_hadir ?? 0);
        @endphp
        <article class="card p-4">
            <h3 class="font-semibold text-slate-900">{{ $a->judul }}</h3>
            <p class="mt-1 text-sm text-slate-600">{{ $a->mulai_pada->format('d/m/Y H:i') }}</p>
            <p class="text-xs text-slate-500">
                {{ $a->status->label() }}
                @if($a->tipe === ActivityKind::Hajatan) &middot; Hajatan @endif
                &middot; {{ $hadir }} hadir
            </p>
            <a href="{{ route('admin.activities.show', $a) }}" class="btn-secondary mt-3 w-full justify-center py-2 text-xs">Detail</a>
        </article>
    @empty
        <p class="card p-6 text-center text-slate-500">Belum ada kegiatan</p>
    @endforelse
</div>
<div class="card hidden md:block">
    <div class="kt-table-wrap">
        <table class="kt-table-responsive min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left">Judul</th>
                    <th class="px-4 py-2 text-left">Waktu</th>
                    <th class="px-4 py-2 text-left">Tipe</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-center">Hadir</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $a)
                    @php
                        $hadir = ($a->tipe === ActivityKind::Hajatan)
                            ? ($a->absensi_shift_hadir ?? 0)
                            : ($a->absensi_biasa_hadir ?? 0);
                    @endphp
                    <tr class="border-t">
                        <td data-label="Judul" class="px-4 py-2 font-medium">{{ $a->judul }}</td>
                        <td data-label="Waktu" class="px-4 py-2">{{ $a->mulai_pada->format('d/m/Y H:i') }}</td>
                        <td data-label="Tipe" class="px-4 py-2">{{ $a->tipe?->label() ?? 'Biasa' }}</td>
                        <td data-label="Status" class="px-4 py-2">{{ $a->status->label() }}</td>
                        <td data-label="Hadir" class="px-4 py-2 text-center">{{ $hadir }}</td>
                        <td data-label="" class="kt-actions px-4 py-2 text-right">
                            <a href="{{ route('admin.activities.show', $a) }}" class="text-emerald-600">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada kegiatan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($activities->hasPages())
    <div class="mt-4">{{ $activities->links() }}</div>
@endif
@endsection
