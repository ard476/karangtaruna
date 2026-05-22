@extends('layouts.admin')@section('title','Tambah Transaksi')@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Transaksi</h1>
<div class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.transactions.store') }}">@csrf @include('admin.transactions._form')
<div class="mt-4"><button class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan</button> <a href="{{ route('admin.transactions.index') }}" class="text-sm">Batal</a></div></form></div>
@endsection