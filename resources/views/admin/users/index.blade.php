@extends('layouts.admin')
@section('title', 'Kelola Pengguna')
@section('content')
<x-page-header title="Kelola Pengguna" subtitle="Akun login pengurus & anggota">
<x-slot:actions>
<a href="{{ route('admin.users.create') }}" class="btn-primary w-full sm:w-auto">+ Tambah Akun</a>
</x-slot:actions>
</x-page-header>
<div class="card mb-4 p-4">
<form method="GET" class="space-y-3">
<input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau username..." class="input-touch">
<select name="role" class="select-touch">
<option value="">Semua peran</option>
@foreach(\App\Enums\UserRole::cases() as $role)
<option value="{{ $role->value }}" @selected(($filters['role'] ?? '') === $role->value)>{{ $role->label() }}</option>
@endforeach
</select>
<button class="btn-primary w-full">Filter</button>
</form></</div>
<div class="space-y-3 md:hidden">
@foreach($users as $u)
<article class="card p-4">
<h3 class="font-semibold">{{ $u->name }}</h3>
<p class="text-sm text-slate-600">{{ $u->username }} · {{ $u->roleLabel() }}</p>
<p class="text-xs {{ $u->is_active ? 'text-emerald-600' : 'text-red-600' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</p>
@if(!$u->isSuperAdmin() && $u->id !== auth()->id())
<div class="mt-3 grid grid-cols-2 gap-2">
<a href="{{ route('admin.users.edit', $u) }}" class="btn-secondary justify-center py-2 text-xs text-center">Ubah</a>
<form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus akun?')">@csrf @method('DELETE')
<button class="btn-danger w-full py-2 text-xs">Hapus</button></form></</div>
@elseif($u->isSuperAdmin())<p class="mt-2 text-xs text-slate-400">Akun sistem</p>@endif
</article>
@endforeach</</div>
<div class="card hidden md:block overflow-hidden">
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
</tbody></table></</div>
@if($users->hasPages())<div class="mt-4">{{ $users->links() }}</</div>@endif
@endsection