@extends('layouts.admin')@section('title','Ubah Transaksi')@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Transaksi</h1>
<div class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.transactions.update',$transaction) }}">@csrf @method('PUT') @include('admin.transactions._form')
<div class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button>
<form method="POST" action="{{ route('admin.transactions.destroy',$transaction) }}" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="rounded border border-red-200 px-4 py-2 text-sm text-red-600">Hapus</button></form></div></form></div>
@endsection