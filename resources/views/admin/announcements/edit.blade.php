@extends('layouts.admin')@section('title','Ubah Pengumuman')@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Pengumuman</h1>
<div class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.announcements.update',$announcement) }}">@csrf @method('PUT') @include('admin.announcements._form')
<div class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button>
<form method="POST" action="{{ route('admin.announcements.destroy',$announcement) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600 text-sm">Hapus</button></form></div></form></div>
@endsection