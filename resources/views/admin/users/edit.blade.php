@extends('layouts.admin')
@section('title', 'Ubah Pengguna')
@section('content')
<x-page-header title="Ubah Akun" :subtitle="$user->username" />
<div class="card p-4 sm:p-6">
<form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
@include('admin.users._form')
<div class="mt-6 flex gap-2">
<button type="submit" class="btn-primary flex-1 sm:flex-none">Simpan</button>
<a href="{{ route('admin.users.index') }}" class="btn-secondary flex-1 sm:flex-none text-center">Batal</a>
</</div></form></</div>
@endsection