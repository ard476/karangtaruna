@extends('layouts.member')@section('title',$announcement->judul)@section('content')
<a href="{{ route('member.announcements.index') }}" class="text-sm text-emerald-600">&larr; Kembali</a>
<h1 class="text-2xl font-bold mt-2">{{ $announcement->judul }}</h1>
<p class="text-xs text-slate-500 mb-4">{{ $announcement->published_at?->format('d F Y H:i') }}</p>
<div class="rounded-xl border bg-white p-6"><p class="whitespace-pre-wrap">{{ $announcement->isi }}</p></div>
@endsection