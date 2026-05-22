@extends('layouts.member')
@section('title', 'Kegiatan')
@section('content')
<h1 class="text-2xl font-bold mb-4">Kegiatan</h1>

<section class="mb-5">
    <div class="mb-3 flex items-center justify-between gap-2">
        <h2 class="font-semibold text-slate-900">Sedang Berlangsung</h2>
        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">{{ $ongoingActivities->count() }} aktif</span>
    </div>
    <div class="space-y-3">
        @forelse($ongoingActivities as $a)
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <a href="{{ route('member.activities.show', $a) }}" class="font-semibold text-emerald-800">{{ $a->judul }}</a>
                <p class="text-sm text-slate-700 mt-1">
                    {{ $a->mulai_pada->format('d F Y, H:i') }}
                    @if($a->selesai_pada) &mdash; {{ $a->selesai_pada->format('H:i') }} @endif
                    @if($a->lokasi) &middot; {{ $a->lokasi }} @endif
                </p>
                <p class="text-xs mt-1 text-emerald-700">
                    Bisa absen sekarang
                    @if($a->isHajatan()) &middot; Hajatan (shift + foto) @endif
                </p>
                @if(auth()->user()->member)
                    @if($a->isHajatan())
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <a href="{{ route('member.activities.show', $a) }}" class="btn-primary text-center text-sm py-2.5">Absen</a>
                            <a href="{{ route('member.activities.show', $a) }}" class="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2.5 text-center text-sm font-medium text-amber-800">Izin</a>
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Pilih shift di halaman detail.</p>
                    @else
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <form method="POST" action="{{ route('member.activities.attendance.store', $a) }}">
                                @csrf
                                <input type="hidden" name="status" value="hadir">
                                <button type="submit" class="btn-primary w-full py-2.5 text-sm">Absen</button>
                            </form>
                            <form method="POST" action="{{ route('member.activities.attendance.store', $a) }}">
                                @csrf
                                <input type="hidden" name="status" value="izin">
                                <button type="submit" class="w-full rounded-lg border border-amber-300 bg-amber-50 px-3 py-2.5 text-sm font-medium text-amber-800">Izin</button>
                            </form>
                        </div>
                    @endif
                @else
                    <p class="mt-3 rounded-lg bg-amber-50 p-2 text-xs text-amber-800">Akun belum terhubung ke data anggota.</p>
                @endif
            </div>
        @empty
            <p class="rounded-xl border bg-white p-4 text-sm text-slate-500">Tidak ada kegiatan yang sedang berlangsung.</p>
        @endforelse
    </div>
</section>

<section>
<h2 class="mb-3 font-semibold text-slate-900">Kegiatan Mendatang</h2>
<div class="space-y-3">
    @forelse($upcomingActivities as $a)
        <div class="rounded-xl border bg-white p-4">
            <a href="{{ route('member.activities.show', $a) }}" class="font-semibold text-emerald-700">{{ $a->judul }}</a>
            <p class="text-sm text-slate-600 mt-1">{{ $a->mulai_pada->format('d F Y, H:i') }} @if($a->lokasi) &mdash; {{ $a->lokasi }} @endif</p>
            <p class="text-xs mt-1 text-slate-500">
                {{ $a->status->label() }}
                @if($a->isHajatan()) &middot; <span class="text-amber-700">Hajatan (shift + foto)</span> @endif
            </p>
            @if(auth()->user()->member)
                <div class="mt-3 grid grid-cols-2 gap-2">
                    <button type="button" disabled class="w-full rounded-lg bg-slate-100 px-3 py-2.5 text-sm font-medium text-slate-500">Absen</button>
                    <button type="button" disabled class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-500">Izin</button>
                </div>
                <p class="mt-1 text-[11px] text-slate-500">Aktif saat kegiatan/shift berlangsung.</p>
                <a href="{{ route('member.activities.show', $a) }}" class="mt-2 block text-center text-xs font-medium text-emerald-700">Lihat detail</a>
            @else
                <p class="mt-3 rounded-lg bg-amber-50 p-2 text-xs text-amber-800">Akun belum terhubung ke data anggota.</p>
            @endif
        </div>
    @empty
        <p class="rounded-xl border bg-white p-4 text-sm text-slate-500">Belum ada kegiatan mendatang.</p>
    @endforelse
</div>
{{ $upcomingActivities->links() }}
</section>
@endsection
