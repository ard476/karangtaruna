@extends('layouts.admin')@section('title','Pengumuman')@section('content')
<div class="mb-6 flex justify-between"><h1 class="text-2xl font-bold">Pengumuman</h1>@if(auth()->user()->hasPermission('announcements.manage'))<a href="{{ route('admin.announcements.create') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">+ Tambah</a>@endif</div>
<div class="space-y-3">@forelse($announcements as $a)
<div class="rounded-xl border bg-white p-4 flex justify-between gap-4"><div><h3 class="font-semibold">{{ $a->judul }}</h3><p class="text-sm text-slate-600 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($a->isi), 120) }}</p><p class="text-xs text-slate-400 mt-1">{{ $a->is_published?'Terbit':'Draft' }} | {{ $a->created_at->format('d/m/Y') }}</p></div>
<div class="text-sm whitespace-nowrap"><a href="{{ route('admin.announcements.show',$a) }}" class="text-emerald-600">Baca</a>@if(auth()->user()->hasPermission('announcements.manage')) | <a href="{{ route('admin.announcements.edit',$a) }}">Ubah</a>@endif</div></div>
@empty<p class="text-slate-500">Belum ada pengumuman.</p>@endforelse</div>
<div class="mt-4">{{ $announcements->links() }}</div>
@endsection