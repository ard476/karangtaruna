@extends('layouts.admin')
@section('title','Ubah Kegiatan')
@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Kegiatan</h1>
<div class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.activities.update',$activity) }}">@csrf @method('PUT') @include('admin.activities._form')
<div class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-white text-sm">Simpan</button><a href="{{ route('admin.activities.show',$activity) }}" class="rounded border px-4 py-2 text-sm">Batal</a></div></form></div>
@endsection