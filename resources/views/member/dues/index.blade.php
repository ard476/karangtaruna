@extends('layouts.member')@section('title','Iuran Saya')@section('content')
<h1 class="text-2xl font-bold mb-4">Iuran Saya</h1>
@if(!$member)<p class="text-amber-700 text-sm">Data keanggotaan belum terhubung.</p>@else
<div class="rounded-xl border bg-white overflow-hidden"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-2 text-left">Periode</th><th class="text-right">Nominal</th><th>Jatuh Tempo</th><th>Status</th></tr></thead><tbody>
@forelse($payments as $p)<tr class="border-t"><td class="px-4 py-2">{{ $p->period->judul }}</td><td class="px-4 py-2 text-right">@rupiah($p->period->jumlah)</td><td class="px-4 py-2">{{ $p->period->jatuh_tempo->format('d/m/Y') }}</td><td class="px-4 py-2 {{ $p->status->value==='lunas'?'text-emerald-600':'text-amber-600' }}">{{ $p->status->label() }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada tagihan iuran.</td></tr>@endforelse
</tbody></table></div>@endif
@endsection