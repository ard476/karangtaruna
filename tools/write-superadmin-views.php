<?php

$t = 'd'.'iv';
$o = '<'.$t;
$c = '</'.$t.'>';
$r = fn (string $s) => str_replace(['__O__', '__C__'], [$o, $c], $s);

$views = [];

$views['admin/organization/edit.blade.php'] = $r(<<<'BLADE'
@extends('layouts.admin')
@section('title', 'Profil Organisasi')
@section('content')
<x-page-header title="Profil Organisasi" subtitle="Data Karang Taruna & wilayah RT" />
__O__ class="card p-4 sm:p-6">
<form method="POST" action="{{ route('admin.organization.update') }}">@csrf @method('PUT')
__O__ class="grid gap-4 sm:grid-cols-2">
__O__><label class="text-sm font-medium">Nama Karang Taruna *</label>
<input name="name" value="{{ old('name', $organization->name) }}" required class="input-touch"></__C__
__O__><label class="text-sm font-medium">Dusun *</label>
<input name="dusun" value="{{ old('dusun', $organization->dusun) }}" required class="input-touch"></__C__
__O__><label class="text-sm font-medium">RW (nomor) *</label>
<input name="rw_number" value="{{ old('rw_number', $organization->rw_number) }}" required class="input-touch"></__C__
__O__><label class="text-sm font-medium">Nama RW</label>
<input name="rw_name" value="{{ old('rw_name', $organization->rw_name) }}" class="input-touch"></__C__
__O__><label class="text-sm font-medium">Desa *</label>
<input name="desa" value="{{ old('desa', $organization->desa) }}" required class="input-touch"></__C__
__O__><label class="text-sm font-medium">Kecamatan *</label>
<input name="kecamatan" value="{{ old('kecamatan', $organization->kecamatan) }}" required class="input-touch"></__C__
__O__><label class="text-sm font-medium">Kabupaten *</label>
<input name="kabupaten" value="{{ old('kabupaten', $organization->kabupaten) }}" required class="input-touch"></__C__
__O__><label class="text-sm font-medium">Tahun Berdiri</label>
<input type="number" name="tahun_berdiri" value="{{ old('tahun_berdiri', $organization->tahun_berdiri) }}" class="input-touch"></__C__
__O__><label class="text-sm font-medium">Telepon</label>
<input name="phone" value="{{ old('phone', $organization->phone) }}" class="input-touch"></__C__
__O__><label class="text-sm font-medium">Email</label>
<input type="email" name="email" value="{{ old('email', $organization->email) }}" class="input-touch"></__C__
__C__
__O__><label class="text-sm font-medium">Alamat Lengkap</label>
<textarea name="alamat_lengkap" rows="2" class="input-touch">{{ old('alamat_lengkap', $organization->alamat_lengkap) }}</textarea></__C__
<h3 class="mt-4 font-semibold text-slate-900">Data RT (3 RT)</h3>
@foreach($organization->rts as $rt)
__O__ class="mt-2 rounded-xl bg-slate-50 p-3">
<input type="hidden" name="rts[{{ $loop->index }}][id]" value="{{ $rt->id }}">
<label class="text-sm font-medium">RT {{ $rt->number }} — nama opsional</label>
<input name="rts[{{ $loop->index }}][name]" value="{{ old('rts.'.$loop->index.'.name', $rt->name) }}" class="input-touch mt-1" placeholder="Contoh: RT {{ $rt->number }} Utara">
</__C__
@endforeach
__O__ class="mt-6"><button type="submit" class="btn-primary w-full sm:w-auto">Simpan Perubahan</button></__C__
</form></__C__
@endsection
BLADE);

$views['admin/users/index.blade.php'] = $r(<<<'BLADE'
@extends('layouts.admin')
@section('title', 'Kelola Pengguna')
@section('content')
<x-page-header title="Kelola Pengguna" subtitle="Akun login pengurus & anggota">
<x-slot:actions>
<a href="{{ route('admin.users.create') }}" class="btn-primary w-full sm:w-auto">+ Tambah Akun</a>
</x-slot:actions>
</x-page-header>
__O__ class="card mb-4 p-4">
<form method="GET" class="space-y-3">
<input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau username..." class="input-touch">
<select name="role" class="select-touch">
<option value="">Semua peran</option>
@foreach(\App\Enums\UserRole::cases() as $role)
<option value="{{ $role->value }}" @selected(($filters['role'] ?? '') === $role->value)>{{ $role->label() }}</option>
@endforeach
</select>
<button class="btn-primary w-full">Filter</button>
</form></__C__
__O__ class="space-y-3 md:hidden">
@foreach($users as $u)
<article class="card p-4">
<h3 class="font-semibold">{{ $u->name }}</h3>
<p class="text-sm text-slate-600">@{{ $u->username }} · {{ $u->roleLabel() }}</p>
<p class="text-xs {{ $u->is_active ? 'text-emerald-600' : 'text-red-600' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</p>
@if(!$u->isSuperAdmin() && $u->id !== auth()->id())
__O__ class="mt-3 grid grid-cols-2 gap-2">
<a href="{{ route('admin.users.edit', $u) }}" class="btn-secondary justify-center py-2 text-xs text-center">Ubah</a>
<form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus akun?')">@csrf @method('DELETE')
<button class="btn-danger w-full py-2 text-xs">Hapus</button></form></__C__
@elseif($u->isSuperAdmin())<p class="mt-2 text-xs text-slate-400">Akun sistem</p>@endif
</article>
@endforeach</__C__
__O__ class="card hidden md:block overflow-hidden">
<table class="min-w-full text-sm"><thead class="bg-slate-50"><tr>
<th class="px-4 py-2 text-left">Nama</th><th>Username</th><th>Peran</th><th>Status</th><th></th>
</tr></thead><tbody>
@foreach($users as $u)
<tr class="border-t">
<td class="px-4 py-2 font-medium">{{ $u->name }}</td>
<td class="px-4 py-2">{{ $u->username }}</td>
<td class="px-4 py-2">{{ $u->roleLabel() }}</td>
<td class="px-4 py-2 {{ $u->is_active ? 'text-emerald-600' : 'text-red-600' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</td>
<td class="px-4 py-2 text-right">
@if(!$u->isSuperAdmin())
<a href="{{ route('admin.users.edit', $u) }}" class="text-emerald-600">Ubah</a>
@if($u->id !== auth()->id())
<form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')
<button class="ml-2 text-red-600">Hapus</button></form>@endif
@else<span class="text-slate-400">—</span>@endif
</td></tr>
@endforeach
</tbody></table></__C__
@if($users->hasPages())__O__ class="mt-4">{{ $users->links() }}</__C__@endif
@endsection
BLADE);

$views['admin/users/_form.blade.php'] = $r(<<<'BLADE'
__O__ class="grid gap-4 sm:grid-cols-2">
__O__><label class="text-sm font-medium">Nama *</label>
<input name="name" value="{{ old('name', $user->name) }}" required class="input-touch"></__C__
__O__><label class="text-sm font-medium">Username *</label>
<input name="username" value="{{ old('username', $user->username) }}" required class="input-touch"></__C__
__O__><label class="text-sm font-medium">Email</label>
<input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-touch"></__C__
__O__><label class="text-sm font-medium">Telepon</label>
<input name="phone" value="{{ old('phone', $user->phone) }}" class="input-touch"></__C__
__O__><label class="text-sm font-medium">Peran *</label>
<select name="role" required class="select-touch">
@foreach($roles as $role)
<option value="{{ $role->value }}" @selected(old('role', $user->role?->value) === $role->value)>{{ $role->label() }}</option>
@endforeach
</select></__C__
__O__ class="flex items-center gap-2 sm:col-span-2">
<input type="hidden" name="is_active" value="0">
<input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $user->is_active ?? true)) class="h-5 w-5 rounded">
<label for="is_active" class="text-sm">Akun aktif</label></__C__
__C__
__O__><label class="text-sm font-medium">Kata Sandi {{ isset($user->id) ? '(kosongkan jika tidak diubah)' : '*' }}</label>
<input type="password" name="password" class="input-touch" {{ isset($user->id) ? '' : 'required' }}>
<input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" class="input-touch mt-2"></__C__
BLADE);

$views['admin/users/create.blade.php'] = $r(<<<'BLADE'
@extends('layouts.admin')
@section('title', 'Tambah Pengguna')
@section('content')
<x-page-header title="Tambah Akun" subtitle="Buat login pengurus atau anggota" />
__O__ class="card p-4 sm:p-6">
<form method="POST" action="{{ route('admin.users.store') }}">@csrf
@include('admin.users._form')
__O__ class="mt-6 flex gap-2">
<button type="submit" class="btn-primary flex-1 sm:flex-none">Simpan</button>
<a href="{{ route('admin.users.index') }}" class="btn-secondary flex-1 sm:flex-none text-center">Batal</a>
</__C__</form></__C__
@endsection
BLADE);

$views['admin/users/edit.blade.php'] = $r(<<<'BLADE'
@extends('layouts.admin')
@section('title', 'Ubah Pengguna')
@section('content')
<x-page-header title="Ubah Akun" :subtitle="$user->username" />
__O__ class="card p-4 sm:p-6">
<form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
@include('admin.users._form')
__O__ class="mt-6 flex gap-2">
<button type="submit" class="btn-primary flex-1 sm:flex-none">Simpan</button>
<a href="{{ route('admin.users.index') }}" class="btn-secondary flex-1 sm:flex-none text-center">Batal</a>
</__C__</form></__C__
@endsection
BLADE);

$base = dirname(__DIR__).'/resources/views';
foreach ($views as $path => $content) {
    $full = $base.'/'.$path;
    if (! is_dir(dirname($full))) {
        mkdir(dirname($full), 0755, true);
    }
    file_put_contents($full, $content);
}
echo "views ok\n";
