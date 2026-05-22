@extends('layouts.admin')
@section('title','Keuangan')
@section('content')
<div class="mb-6 flex justify-between"><h1 class="text-2xl font-bold">Keuangan</h1>@if(auth()->user()->hasPermission('finance.manage'))<a href="{{ route('admin.transactions.create') }}" class="btn-primary px-4 py-2 text-sm text-white">+ Transaksi</a>@endif</div>
<div class="grid gap-4 sm:grid-cols-3 mb-6"><div class="rounded-xl border bg-white p-4"><p class="text-xs text-slate-500">Saldo</p><p class="text-xl font-bold">@rupiah($saldo)</p></div>><div class="rounded-xl border bg-white p-4"><p class="text-xs text-slate-500">Pemasukan (filter)</p><p class="text-xl font-bold text-emerald-600">@rupiah($totalMasuk)</p></div>><div class="rounded-xl border bg-white p-4"><p class="text-xs text-slate-500">Pengeluaran (filter)</p><p class="text-xl font-bold text-red-600">@rupiah($totalKeluar)</p></div>></div>
<div class="mb-4 rounded-xl border bg-white p-4"><form class="grid gap-2 sm:grid-cols-5" method="GET">
<select name="tipe" class="rounded border px-2 py-2 text-sm"><option value="">Semua tipe</option>@foreach(\App\Enums\TransactionType::cases() as $tp)<option value="{{ $tp->value }}" @selected(($filters['tipe']??'')==$tp->value)>{{ $tp->label() }}</option>@endforeach</select>
<input type="date" name="dari" value="{{ $filters['dari']??'' }}" class="rounded border px-2 py-2 text-sm">
<input type="date" name="sampai" value="{{ $filters['sampai']??'' }}" class="rounded border px-2 py-2 text-sm">
<button class="rounded bg-slate-800 px-3 py-2 text-sm text-white">Filter</button><a href="{{ route('admin.transactions.index') }}" class="input-touch text-center">Reset</a></form></div>
<div class="rounded-xl border bg-white overflow-hidden"><div class="kt-table-wrap"><table class="kt-table-responsive min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-2 text-left">Tanggal</th><th>Tipe</th><th>Kategori</th><th>Keterangan</th><th class="text-right">Jumlah</th><th></th></tr></thead><tbody>
@foreach($transactions as $tr)<tr class="border-t"><td class="px-4 py-2">{{ $tr->tanggal->format('d/m/Y') }}</td><td class="px-4 py-2">{{ $tr->tipe->label() }}</td><td class="px-4 py-2">{{ $tr->kategoriLabel() }}</td><td class="px-4 py-2">{{ $tr->keterangan }}</td><td class="px-4 py-2 text-right font-medium {{ $tr->tipe->value==='pemasukan'?'text-emerald-600':'text-red-600' }}">@rupiah($tr->jumlah)</td>
<td class="px-4 py-2">@if(auth()->user()->hasPermission('finance.manage'))<a href="{{ route('admin.transactions.edit',$tr) }}" class="text-emerald-600">Ubah</a>@endif</td></tr>@endforeach
</tbody></table></div><div class="p-3">{{ $transactions->links() }}</div>></div>
@endsection