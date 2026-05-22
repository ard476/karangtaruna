<?php

$base = dirname(__DIR__).'/resources/views';
$t = 'd'.'iv';
$open = '<'.$t;
$close = '</'.$t.'>';
$c = fn (string $s) => str_replace(['__D__', '__/D__'], [$open, $close], $s);

$views = [];

$views['admin/transactions/index.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')
@section('title','Keuangan')
@section('content')
__D__ class="mb-6 flex justify-between"><h1 class="text-2xl font-bold">Keuangan</h1>@if(auth()->user()->hasPermission('finance.manage'))<a href="{{ route('admin.transactions.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">+ Transaksi</a>@endif</__/D__
__D__ class="grid gap-4 sm:grid-cols-3 mb-6"><__D__ class="rounded-xl border bg-white p-4"><p class="text-xs text-slate-500">Saldo</p><p class="text-xl font-bold">@rupiah($saldo)</p></__/D__><__D__ class="rounded-xl border bg-white p-4"><p class="text-xs text-slate-500">Pemasukan (filter)</p><p class="text-xl font-bold text-emerald-600">@rupiah($totalMasuk)</p></__/D__><__D__ class="rounded-xl border bg-white p-4"><p class="text-xs text-slate-500">Pengeluaran (filter)</p><p class="text-xl font-bold text-red-600">@rupiah($totalKeluar)</p></__/D__></__/D__
__D__ class="mb-4 rounded-xl border bg-white p-4"><form class="grid gap-2 sm:grid-cols-5" method="GET">
<select name="tipe" class="rounded border px-2 py-2 text-sm"><option value="">Semua tipe</option>@foreach(\App\Enums\TransactionType::cases() as $tp)<option value="{{ $tp->value }}" @selected(($filters['tipe']??'')==$tp->value)>{{ $tp->label() }}</option>@endforeach</select>
<input type="date" name="dari" value="{{ $filters['dari']??'' }}" class="rounded border px-2 py-2 text-sm">
<input type="date" name="sampai" value="{{ $filters['sampai']??'' }}" class="rounded border px-2 py-2 text-sm">
<button class="rounded bg-slate-800 px-3 py-2 text-sm text-white">Filter</button><a href="{{ route('admin.transactions.index') }}" class="rounded border px-3 py-2 text-sm text-center">Reset</a></form></__/D__
__D__ class="rounded-xl border bg-white overflow-hidden"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-2 text-left">Tanggal</th><th>Tipe</th><th>Kategori</th><th>Keterangan</th><th class="text-right">Jumlah</th><th></th></tr></thead><tbody>
@foreach($transactions as $tr)<tr class="border-t"><td class="px-4 py-2">{{ $tr->tanggal->format('d/m/Y') }}</td><td class="px-4 py-2">{{ $tr->tipe->label() }}</td><td class="px-4 py-2">{{ $tr->kategoriLabel() }}</td><td class="px-4 py-2">{{ $tr->keterangan }}</td><td class="px-4 py-2 text-right font-medium {{ $tr->tipe->value==='pemasukan'?'text-emerald-600':'text-red-600' }}">@rupiah($tr->jumlah)</td>
<td class="px-4 py-2">@if(auth()->user()->hasPermission('finance.manage'))<a href="{{ route('admin.transactions.edit',$tr) }}" class="text-emerald-600">Ubah</a>@endif</td></tr>@endforeach
</tbody></table><__D__ class="p-3">{{ $transactions->links() }}</__/D__></__/D__
@endsection
BLADE);

$views['admin/transactions/_form.blade.php'] = $c(<<<'BLADE'
__D__ class="grid gap-4 sm:grid-cols-2">
__D__><label class="text-sm font-medium">Tipe</label><select name="tipe" id="tipe" class="w-full rounded border px-3 py-2 text-sm" required>@foreach(\App\Enums\TransactionType::cases() as $tp)<option value="{{ $tp->value }}" @selected(old('tipe',$transaction->tipe?->value)==$tp->value)>{{ $tp->label() }}</option>@endforeach</select></__/D__
__D__><label class="text-sm font-medium">Tanggal</label><input type="date" name="tanggal" value="{{ old('tanggal',$transaction->tanggal?->format('Y-m-d')) }}" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
__D__><label class="text-sm font-medium">Kategori</label><select name="kategori" id="kategori" class="w-full rounded border px-3 py-2 text-sm" required></select></__/D__
__D__><label class="text-sm font-medium">Jumlah (Rp)</label><input type="number" name="jumlah" value="{{ old('jumlah',$transaction->jumlah) }}" min="1" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
__/D__
__D__><label class="text-sm font-medium">Keterangan</label><input name="keterangan" value="{{ old('keterangan',$transaction->keterangan) }}" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
<script>
const pemasukan=@json(array_keys(config('finance.kategori_pemasukan'))), pengeluaran=@json(array_keys(config('finance.kategori_pengeluaran')));
const labels={...@json(config('finance.kategori_pemasukan')), ...@json(config('finance.kategori_pengeluaran'))};
const sel=document.getElementById('kategori'), tipe=document.getElementById('tipe'), cur='{{ old('kategori',$transaction->kategori) }}';
function fill(){const list=tipe.value==='pengeluaran'?pengeluaran:pemasukan; sel.innerHTML=list.map(k=>`<option value="${k}" ${k===cur?'selected':''}>${labels[k]||k}</option>`).join('');}
tipe.addEventListener('change',()=>{cur='';fill();}); fill();
</script>
BLADE);

$views['admin/transactions/create.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Tambah Transaksi')@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Transaksi</h1>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.transactions.store') }}">@csrf @include('admin.transactions._form')
__D__ class="mt-4"><button class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button> <a href="{{ route('admin.transactions.index') }}" class="text-sm">Batal</a></__/D__</form></__/D__
@endsection
BLADE);

$views['admin/transactions/edit.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Ubah Transaksi')@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Transaksi</h1>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.transactions.update',$transaction) }}">@csrf @method('PUT') @include('admin.transactions._form')
__D__ class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button>
<form method="POST" action="{{ route('admin.transactions.destroy',$transaction) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="rounded border border-red-200 px-4 py-2 text-sm text-red-600">Hapus</button></form></__/D__</form></__/D__
@endsection
BLADE);

$views['admin/dues/index.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Iuran')@section('content')
__D__ class="mb-6 flex justify-between"><h1 class="text-2xl font-bold">Iuran Anggota</h1>@if(auth()->user()->hasPermission('finance.manage'))<a href="{{ route('admin.dues.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">+ Periode Baru</a>@endif</__/D__
__D__ class="rounded-xl border bg-white overflow-hidden"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-2 text-left">Periode</th><th>Jatuh Tempo</th><th class="text-right">Nominal</th><th>Lunas</th><th></th></tr></thead><tbody>
@forelse($periods as $p)<tr class="border-t"><td class="px-4 py-2 font-medium">{{ $p->judul }}</td><td class="px-4 py-2">{{ $p->jatuh_tempo->format('d/m/Y') }}</td><td class="px-4 py-2 text-right">@rupiah($p->jumlah)</td><td class="px-4 py-2 text-center">{{ $p->lunas_count }}/{{ $p->payments_count }}</td><td class="px-4 py-2 text-right"><a href="{{ route('admin.dues.show',$p) }}" class="text-emerald-600">Kelola</a></td></tr>@empty<tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Belum ada periode iuran</td></tr>@endforelse
</tbody></table><__D__ class="p-3">{{ $periods->links() }}</__/D__></__/D__
@endsection
BLADE);

$views['admin/dues/_form.blade.php'] = $c(<<<'BLADE'
__D__ class="grid gap-4 sm:grid-cols-2">
__D__><label class="text-sm font-medium">Judul Periode *</label><input name="judul" value="{{ old('judul',$period->judul) }}" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
__D__><label class="text-sm font-medium">Nominal (Rp) *</label><input type="number" name="jumlah" value="{{ old('jumlah',$period->jumlah) }}" min="1" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
__D__><label class="text-sm font-medium">Jatuh Tempo *</label><input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo',$period->jatuh_tempo?->format('Y-m-d')) }}" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
__D__ class="flex items-center gap-2 pt-6"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$period->is_active??true))><label class="text-sm">Periode aktif</label></__/D__
__/D__
BLADE);

$views['admin/dues/create.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Tambah Periode Iuran')@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Periode Iuran</h1>
<p class="text-sm text-slate-600 mb-4">Tagihan otomatis dibuat untuk semua anggota aktif.</p>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.dues.store') }}">@csrf @include('admin.dues._form')
<button class="mt-4 rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button></form></__/D__
@endsection
BLADE);

$views['admin/dues/edit.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Ubah Periode')@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Periode Iuran</h1>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.dues.update',$period) }}">@csrf @method('PUT') @include('admin.dues._form')
__D__ class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button>
<form method="POST" action="{{ route('admin.dues.destroy',$period) }}" onsubmit="return confirm('Hapus periode?')">@csrf @method('DELETE')<button class="text-red-600 text-sm">Hapus Periode</button></form></__/D__</form></__/D__
@endsection
BLADE);

$views['admin/dues/show.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title',$period->judul)@section('content')
__D__ class="mb-4"><a href="{{ route('admin.dues.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a><h1 class="text-2xl font-bold">{{ $period->judul }}</h1><p class="text-sm text-slate-600">Nominal @rupiah($period->jumlah) | Jatuh tempo {{ $period->jatuh_tempo->format('d F Y') }}</p></__/D__
__D__ class="rounded-xl border bg-white overflow-hidden"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-2 text-left">Anggota</th><th>RT</th><th>Status</th>@if(auth()->user()->hasPermission('finance.manage'))<th>Aksi</th>@endif</tr></thead><tbody>
@foreach($payments as $pay)<tr class="border-t"><td class="px-4 py-2">{{ $pay->member->nama_lengkap }}</td><td class="px-4 py-2">{{ $pay->member->rt->label() }}</td>
<td class="px-4 py-2"><span class="{{ $pay->status->value==='lunas'?'text-emerald-600':'text-amber-600' }}">{{ $pay->status->label() }}</span>@if($pay->dibayar_pada)<span class="text-xs text-slate-500 block">{{ $pay->dibayar_pada->format('d/m/Y') }}</span>@endif</td>
@if(auth()->user()->hasPermission('finance.manage'))<td class="px-4 py-2">@if($pay->status->value!=='lunas')<form method="POST" action="{{ route('admin.dues.payments.update',[$period,$pay]) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="lunas"><button class="text-sm text-emerald-600">Tandai Lunas</button></form>@else<form method="POST" action="{{ route('admin.dues.payments.update',[$period,$pay]) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="belum_bayar"><button class="text-sm text-slate-500">Batalkan</button></form>@endif</td>@endif</tr>@endforeach
</tbody></table></__/D__
@endsection
BLADE);

$views['admin/announcements/index.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Pengumuman')@section('content')
__D__ class="mb-6 flex justify-between"><h1 class="text-2xl font-bold">Pengumuman</h1>@if(auth()->user()->hasPermission('announcements.manage'))<a href="{{ route('admin.announcements.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">+ Tambah</a>@endif</__/D__
__D__ class="space-y-3">@forelse($announcements as $a)
__D__ class="rounded-xl border bg-white p-4 flex justify-between gap-4"><__D__><h3 class="font-semibold">{{ $a->judul }}</h3><p class="text-sm text-slate-600 line-clamp-2">{{ Str::limit(strip_tags($a->isi),120) }}</p><p class="text-xs text-slate-400 mt-1">{{ $a->is_published?'Terbit':'Draft' }} | {{ $a->created_at->format('d/m/Y') }}</p></__/D__
__D__ class="text-sm whitespace-nowrap"><a href="{{ route('admin.announcements.show',$a) }}" class="text-emerald-600">Baca</a>@if(auth()->user()->hasPermission('announcements.manage')) | <a href="{{ route('admin.announcements.edit',$a) }}">Ubah</a>@endif</__/D__</__/D__
@empty<p class="text-slate-500">Belum ada pengumuman.</p>@endforelse</__/D__
__D__ class="mt-4">{{ $announcements->links() }}</__/D__
@endsection
BLADE);

$views['admin/announcements/_form.blade.php'] = $c(<<<'BLADE'
__D__ class="space-y-4">
__D__><label class="text-sm font-medium">Judul *</label><input name="judul" value="{{ old('judul',$announcement->judul) }}" required class="w-full rounded border px-3 py-2 text-sm"></__/D__
__D__><label class="text-sm font-medium">Isi *</label><textarea name="isi" rows="8" required class="w-full rounded border px-3 py-2 text-sm">{{ old('isi',$announcement->isi) }}</textarea></__/D__
__D__ class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$announcement->is_published))><label class="text-sm">Terbitkan sekarang</label></__/D__
__D__><label class="text-sm font-medium">Kadaluarsa (opsional)</label><input type="datetime-local" name="expires_at" value="{{ old('expires_at',$announcement->expires_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded border px-3 py-2 text-sm"></__/D__
__/D__
BLADE);

$views['admin/announcements/create.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Tambah Pengumuman')@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Pengumuman</h1>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.announcements.store') }}">@csrf @include('admin.announcements._form')<button class="mt-4 rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button></form></__/D__
@endsection
BLADE);

$views['admin/announcements/edit.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Ubah Pengumuman')@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Pengumuman</h1>
__D__ class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.announcements.update',$announcement) }}">@csrf @method('PUT') @include('admin.announcements._form')
__D__ class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button>
<form method="POST" action="{{ route('admin.announcements.destroy',$announcement) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600 text-sm">Hapus</button></form></__/D__</form></__/D__
@endsection
BLADE);

$views['admin/announcements/show.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title',$announcement->judul)@section('content')
<a href="{{ route('admin.announcements.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a>
<h1 class="text-2xl font-bold mt-2">{{ $announcement->judul }}</h1>
<p class="text-xs text-slate-500 mb-4">{{ $announcement->is_published?'Terbit':'Draft' }} | {{ $announcement->created_at->format('d F Y H:i') }}</p>
__D__ class="rounded-xl border bg-white p-6 prose prose-sm max-w-none"><p class="whitespace-pre-wrap">{{ $announcement->isi }}</p></__/D__
@endsection
BLADE);

$views['admin/reports/index.blade.php'] = $c(<<<'BLADE'
@extends('layouts.admin')@section('title','Laporan')@section('content')
__D__ class="mb-6 flex justify-between print:hidden"><h1 class="text-2xl font-bold">Laporan</h1><button onclick="window.print()" class="rounded border px-4 py-2 text-sm">Cetak</button></__/D__
__D__ class="mb-4 rounded-xl border bg-white p-4 print:hidden"><form method="GET" class="flex gap-2 flex-wrap"><input type="date" name="dari" value="{{ $dari }}" class="rounded border px-2 py-2 text-sm"><input type="date" name="sampai" value="{{ $sampai }}" class="rounded border px-2 py-2 text-sm"><button class="rounded bg-slate-800 px-4 py-2 text-sm text-white">Terapkan</button></form></__/D__
__D__ class="mb-6 text-center"><h2 class="text-xl font-bold">{{ $organization?->name }}</h2><p class="text-sm text-slate-600">{{ $organization?->wilayahLabel() }}</p><p class="text-sm">Periode {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}</p></__/D__
__D__ class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
__D__ class="rounded-xl border p-4"><p class="text-xs text-slate-500">Anggota Aktif</p><p class="text-2xl font-bold">{{ $stats['anggota_aktif'] }}</p></__/D__
__D__ class="rounded-xl border p-4"><p class="text-xs text-slate-500">Kegiatan</p><p class="text-2xl font-bold">{{ $stats['kegiatan'] }}</p></__/D__
__D__ class="rounded-xl border p-4"><p class="text-xs text-slate-500">Pemasukan</p><p class="text-2xl font-bold text-emerald-600">@rupiah($pemasukan)</p></__/D__
__D__ class="rounded-xl border p-4"><p class="text-xs text-slate-500">Pengeluaran</p><p class="text-2xl font-bold text-red-600">@rupiah($pengeluaran)</p></__/D__
__/D__
<p class="text-sm mb-4"><strong>Saldo kas saat ini:</strong> @rupiah($saldo)</p>
__D__ class="rounded-xl border bg-white p-4"><h3 class="font-semibold mb-2">Transaksi Terbaru</h3><table class="min-w-full text-sm"><thead><tr class="border-b"><th class="py-1 text-left">Tanggal</th><th>Keterangan</th><th class="text-right">Jumlah</th></tr></thead><tbody>@foreach($transaksi_terbaru as $t)<tr class="border-b"><td class="py-1">{{ $t->tanggal->format('d/m/Y') }}</td><td>{{ $t->keterangan }}</td><td class="text-right">@rupiah($t->jumlah)</td></tr>@endforeach</tbody></table></__/D__
@endsection
BLADE);

$views['member/dashboard.blade.php'] = $c(<<<'BLADE'
@extends('layouts.member')@section('title','Beranda')@section('content')
<h1 class="text-2xl font-bold mb-1">Selamat datang, {{ auth()->user()->name }}</h1>
<p class="text-slate-600 mb-6">Portal anggota Karang Taruna</p>
@if($member)
__D__ class="grid gap-4 sm:grid-cols-2 mb-6"><__D__ class="rounded-xl border bg-white p-4"><p class="text-xs text-slate-500">RT</p><p class="font-semibold">{{ $member->rt->label() }}</p></__/D__><__D__ class="rounded-xl border bg-white p-4"><p class="text-xs text-slate-500">Status</p><p class="font-semibold text-emerald-600">{{ $member->status->label() }}</p></__/D__></__/D__
@endif
__D__ class="grid gap-6 lg:grid-cols-2">
__D__ class="rounded-xl border bg-white p-5"><h2 class="font-semibold mb-3">Kegiatan Mendatang</h2>@forelse($upcomingActivities as $a)<a href="{{ route('member.activities.show',$a) }}" class="block py-2 border-b text-sm"><span class="font-medium">{{ $a->judul }}</span><span class="text-slate-500 block">{{ $a->mulai_pada->format('d/m/Y H:i') }}</span></a>@empty<p class="text-sm text-slate-500">Tidak ada jadwal.</p>@endforelse</__/D__
__D__ class="rounded-xl border bg-white p-5"><h2 class="font-semibold mb-3">Pengumuman</h2>@forelse($announcements as $an)<a href="{{ route('member.announcements.show',$an) }}" class="block py-2 border-b text-sm font-medium">{{ $an->judul }}</a>@empty<p class="text-sm text-slate-500">Tidak ada pengumuman.</p>@endforelse</__/D__
__/D__
@if($unpaidDues->isNotEmpty())
__D__ class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-5"><h2 class="font-semibold text-amber-900 mb-2">Iuran Belum Lunas</h2><ul class="text-sm space-y-1">@foreach($unpaidDues as $d)<li>{{ $d->period->judul }} — @rupiah($d->period->jumlah) (jatuh tempo {{ $d->period->jatuh_tempo->format('d/m/Y') }})</li>@endforeach</ul><a href="{{ route('member.dues.index') }}" class="text-sm text-emerald-700 font-medium mt-2 inline-block">Lihat semua iuran →</a></__/D__
@endif
@endsection
BLADE);

$views['member/activities/index.blade.php'] = $c(<<<'BLADE'
@extends('layouts.member')@section('title','Kegiatan')@section('content')
<h1 class="text-2xl font-bold mb-4">Kegiatan</h1>
__D__ class="space-y-3">@foreach($activities as $a)
__D__ class="rounded-xl border bg-white p-4"><a href="{{ route('member.activities.show',$a) }}" class="font-semibold text-emerald-700">{{ $a->judul }}</a><p class="text-sm text-slate-600 mt-1">{{ $a->mulai_pada->format('d F Y, H:i') }} @if($a->lokasi)— {{ $a->lokasi }}@endif</p><p class="text-xs mt-1">{{ $a->status->label() }}</p></__/D__
@endforeach</__/D__{{ $activities->links() }}
@endsection
BLADE);

$views['member/activities/show.blade.php'] = $c(<<<'BLADE'
@extends('layouts.member')@section('title',$activity->judul)@section('content')
<a href="{{ route('member.activities.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a>
<h1 class="text-2xl font-bold mt-2">{{ $activity->judul }}</h1>
<p class="text-sm text-slate-600 mb-4">{{ $activity->mulai_pada->format('d F Y, H:i') }} @if($activity->lokasi)| {{ $activity->lokasi }}@endif</p>
__D__ class="rounded-xl border bg-white p-6 mb-4">@if($activity->deskripsi)<p class="whitespace-pre-wrap">{{ $activity->deskripsi }}</p>@endif</__/D__
@if($attendance)<__D__ class="rounded-xl border bg-emerald-50 p-4 text-sm"><strong>Kehadiran Anda:</strong> {{ $attendance->status->label() }}</__/D__>@endif
@endsection
BLADE);

$views['member/announcements/index.blade.php'] = $c(<<<'BLADE'
@extends('layouts.member')@section('title','Pengumuman')@section('content')
<h1 class="text-2xl font-bold mb-4">Pengumuman</h1>
__D__ class="space-y-3">@forelse($announcements as $a)
__D__ class="rounded-xl border bg-white p-4"><a href="{{ route('member.announcements.show',$a) }}" class="font-semibold">{{ $a->judul }}</a><p class="text-xs text-slate-500 mt-1">{{ $a->published_at?->format('d F Y') }}</p></__/D__
@empty<p class="text-slate-500">Tidak ada pengumuman.</p>@endforelse</__/D__{{ $announcements->links() }}
@endsection
BLADE);

$views['member/announcements/show.blade.php'] = $c(<<<'BLADE'
@extends('layouts.member')@section('title',$announcement->judul)@section('content')
<a href="{{ route('member.announcements.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a>
<h1 class="text-2xl font-bold mt-2">{{ $announcement->judul }}</h1>
<p class="text-xs text-slate-500 mb-4">{{ $announcement->published_at?->format('d F Y H:i') }}</p>
__D__ class="rounded-xl border bg-white p-6"><p class="whitespace-pre-wrap">{{ $announcement->isi }}</p></__/D__
@endsection
BLADE);

$views['member/dues/index.blade.php'] = $c(<<<'BLADE'
@extends('layouts.member')@section('title','Iuran Saya')@section('content')
<h1 class="text-2xl font-bold mb-4">Iuran Saya</h1>
@if(!$member)<p class="text-amber-700 text-sm">Data keanggotaan belum terhubung.</p>@else
__D__ class="rounded-xl border bg-white overflow-hidden"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-2 text-left">Periode</th><th class="text-right">Nominal</th><th>Jatuh Tempo</th><th>Status</th></tr></thead><tbody>
@forelse($payments as $p)<tr class="border-t"><td class="px-4 py-2">{{ $p->period->judul }}</td><td class="px-4 py-2 text-right">@rupiah($p->period->jumlah)</td><td class="px-4 py-2">{{ $p->period->jatuh_tempo->format('d/m/Y') }}</td><td class="px-4 py-2 {{ $p->status->value==='lunas'?'text-emerald-600':'text-amber-600' }}">{{ $p->status->label() }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada tagihan iuran.</td></tr>@endforelse
</tbody></table></__/D__@endif
@endsection
BLADE);

foreach ($views as $path => $content) {
    $full = $base.'/'.$path;
    if (! is_dir(dirname($full))) {
        mkdir(dirname($full), 0755, true);
    }
    file_put_contents($full, $content);
}

echo 'Generated '.count($views).' module views'.PHP_EOL;
