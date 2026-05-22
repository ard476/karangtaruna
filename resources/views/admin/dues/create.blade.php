@extends('layouts.admin')@section('title','Tambah Periode Iuran')@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Periode Iuran</h1>
<p class="text-sm text-slate-600 mb-4">Tagihan otomatis dibuat untuk semua anggota aktif.</p>
<div class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.dues.store') }}">@csrf @include('admin.dues._form')
<button class="mt-4 rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button></form></div>
@endsection