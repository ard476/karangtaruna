@extends('layouts.admin')
@section('title', 'Profil Organisasi')
@section('content')
<x-page-header title="Profil Organisasi" subtitle="Data Karang Taruna & wilayah RT" />
<div class="card p-4 sm:p-6">
<form method="POST" action="{{ route('admin.organization.update') }}" enctype="multipart/form-data">@csrf @method('PUT')
<div class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
<label class="text-sm font-medium">Logo Organisasi</label>
<div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center">
@if($organization->logoUrl())
<img src="{{ $organization->logoUrl() }}" alt="Logo {{ $organization->name }}" class="h-20 w-20 rounded-xl border bg-white object-contain p-2">
@else
<div class="flex h-20 w-20 items-center justify-center rounded-xl border bg-white text-sm font-bold text-emerald-700">KT</div>
@endif
<div class="flex-1">
<input type="file" name="logo" accept="image/*" class="block w-full text-sm">
<p class="mt-1 text-xs text-slate-500">Format gambar JPG/PNG/WebP, maksimal 2 MB.</p>
@error('logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
</div>
</div>
<div class="grid gap-4 sm:grid-cols-2">
<div><label class="text-sm font-medium">Nama Karang Taruna *</label>
<input name="name" value="{{ old('name', $organization->name) }}" required class="input-touch"></</div>
<div><label class="text-sm font-medium">Dusun *</label>
<input name="dusun" value="{{ old('dusun', $organization->dusun) }}" required class="input-touch"></</div>
<div><label class="text-sm font-medium">RW (nomor) *</label>
<input name="rw_number" value="{{ old('rw_number', $organization->rw_number) }}" required class="input-touch"></</div>
<div><label class="text-sm font-medium">Nama RW</label>
<input name="rw_name" value="{{ old('rw_name', $organization->rw_name) }}" class="input-touch"></</div>
<div><label class="text-sm font-medium">Desa *</label>
<input name="desa" value="{{ old('desa', $organization->desa) }}" required class="input-touch"></</div>
<div><label class="text-sm font-medium">Kecamatan *</label>
<input name="kecamatan" value="{{ old('kecamatan', $organization->kecamatan) }}" required class="input-touch"></</div>
<div><label class="text-sm font-medium">Kabupaten *</label>
<input name="kabupaten" value="{{ old('kabupaten', $organization->kabupaten) }}" required class="input-touch"></</div>
<div><label class="text-sm font-medium">Tahun Berdiri</label>
<input type="number" name="tahun_berdiri" value="{{ old('tahun_berdiri', $organization->tahun_berdiri) }}" class="input-touch"></</div>
<div><label class="text-sm font-medium">Telepon</label>
<input name="phone" value="{{ old('phone', $organization->phone) }}" class="input-touch"></</div>
<div><label class="text-sm font-medium">Email</label>
<input type="email" name="email" value="{{ old('email', $organization->email) }}" class="input-touch"></</div>
</div>
<div><label class="text-sm font-medium">Alamat Lengkap</label>
<textarea name="alamat_lengkap" rows="2" class="input-touch">{{ old('alamat_lengkap', $organization->alamat_lengkap) }}</textarea></</div>
<h3 class="mt-4 font-semibold text-slate-900">Data RT (3 RT)</h3>
@foreach($organization->rts as $rt)
<div class="mt-2 rounded-xl bg-slate-50 p-3">
<input type="hidden" name="rts[{{ $loop->index }}][id]" value="{{ $rt->id }}">
<label class="text-sm font-medium">RT {{ $rt->number }} — nama opsional</label>
<input name="rts[{{ $loop->index }}][name]" value="{{ old('rts.'.$loop->index.'.name', $rt->name) }}" class="input-touch mt-1" placeholder="Contoh: RT {{ $rt->number }} Utara">
</</div>
@endforeach
<div class="mt-6"><button type="submit" class="btn-primary w-full sm:w-auto">Simpan Perubahan</button></</div>
</form></</div>
@endsection