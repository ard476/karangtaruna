@extends('layouts.admin')@section('title',$announcement->judul)@section('content')
<a href="{{ route('admin.announcements.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a>
<h1 class="text-2xl font-bold mt-2">{{ $announcement->judul }}</h1>
<p class="text-xs text-slate-500 mb-4">{{ $announcement->is_published?'Terbit':'Draft' }} | {{ $announcement->created_at->format('d F Y H:i') }}</p>
<div class="rounded-xl border bg-white p-6 prose prose-sm max-w-none"><p class="whitespace-pre-wrap">{{ $announcement->isi }}</p></div>
@endsection