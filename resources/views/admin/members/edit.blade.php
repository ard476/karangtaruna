@extends('layouts.admin')
@section('title', 'Ubah Anggota')
@section('content')
<div class="mb-6"><h1 class="text-2xl font-bold text-slate-900">Ubah Anggota</h1><p class="mt-1 text-sm text-slate-600">Perbarui data anggota</p></div>
<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
<form method="POST" action="{{ route('admin.members.update', $member) }}" class="space-y-6">@csrf @method('PUT')
@include('admin.members._form')
<div class="flex gap-3 border-t border-slate-200 pt-6">
<button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Simpan</button>
<a href="{{ route('admin.members.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-600 hover:bg-slate-50">Batal</a>
</div>
</form>
</div>
@endsection
