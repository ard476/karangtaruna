@extends('layouts.admin')@section('title','Ubah Periode')@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Periode Iuran</h1>
<div class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.dues.update',$period) }}">@csrf @method('PUT') @include('admin.dues._form')
<div class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button>
<form method="POST" action="{{ route('admin.dues.destroy',$period) }}" onsubmit="return confirm('Hapus periode?')">@csrf @method('DELETE')<button class="text-red-600 text-sm">Hapus Periode</button></form></div></form></div>
@endsection