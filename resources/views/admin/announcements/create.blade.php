@extends('layouts.admin')@section('title','Tambah Pengumuman')@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Pengumuman</h1>
<div class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.announcements.store') }}">@csrf @include('admin.announcements._form')<button class="mt-4 rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button></form></div>
@endsection