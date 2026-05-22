@extends('layouts.member')@section('title','Pengumuman')@section('content')
<h1 class="text-2xl font-bold mb-4">Pengumuman</h1>
<div class="space-y-3">@forelse($announcements as $a)
<div class="rounded-xl border bg-white p-4"><a href="{{ route('member.announcements.show',$a) }}" class="font-semibold">{{ $a->judul }}</a><p class="text-xs text-slate-500 mt-1">{{ $a->published_at?->format('d F Y') }}</p></div>
@empty<p class="text-slate-500">Tidak ada pengumuman.</p>@endforelse</div>{{ $announcements->links() }}
@endsection