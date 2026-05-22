@extends('layouts.admin')@section('title','Laporan')@section('content')
<div class="mb-6 flex justify-between print:hidden"><h1 class="text-2xl font-bold">Laporan</h1><button onclick="window.print()" class="rounded border px-4 py-2 text-sm">Cetak</button></div>
<div class="mb-4 rounded-xl border bg-white p-4 print:hidden"><form method="GET" class="flex gap-2 flex-wrap"><input type="date" name="dari" value="{{ $dari }}" class="rounded border px-2 py-2 text-sm"><input type="date" name="sampai" value="{{ $sampai }}" class="rounded border px-2 py-2 text-sm"><button class="rounded bg-slate-800 px-4 py-2 text-sm text-white">Terapkan</button></form></div>
<div class="mb-6 text-center"><h2 class="text-xl font-bold">{{ $organization?->name }}</h2><p class="text-sm text-slate-600">{{ $organization?->wilayahLabel() }}</p><p class="text-sm">Periode {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p></div>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
<div class="rounded-xl border p-4"><p class="text-xs text-slate-500">Anggota Aktif</p><p class="text-2xl font-bold">{{ $stats['anggota_aktif'] }}</p></div>
<div class="rounded-xl border p-4"><p class="text-xs text-slate-500">Kegiatan</p><p class="text-2xl font-bold">{{ $stats['kegiatan'] }}</p></div>
<div class="rounded-xl border p-4"><p class="text-xs text-slate-500">Pemasukan</p><p class="text-2xl font-bold text-emerald-600">@rupiah($pemasukan)</p></div>
<div class="rounded-xl border p-4"><p class="text-xs text-slate-500">Pengeluaran</p><p class="text-2xl font-bold text-red-600">@rupiah($pengeluaran)</p></div>
</div>
<p class="text-sm mb-4"><strong>Saldo kas saat ini:</strong> @rupiah($saldo)</p>
<div class="rounded-xl border bg-white p-4"><h3 class="font-semibold mb-2">Transaksi Terbaru</h3><table class="min-w-full text-sm"><thead><tr class="border-b"><th class="py-1 text-left">Tanggal</th><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead><tbody>@foreach($transaksi_terbaru as $t)<tr class="border-b"><td class="py-1">{{ $t->tanggal->format('d/m/Y') }}</td><td>{{ $t->keterangan }}</td><td class="text-right">@rupiah($t->jumlah)</td></tr>@endforeach</tbody></table></div>
@endsection