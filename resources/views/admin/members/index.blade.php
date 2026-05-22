@extends('layouts.admin')
@section('title', 'Data Anggota')
@section('content')
    <x-page-header title="Data Anggota" subtitle="Kelola anggota per RT">
        <x-slot:actions>
            @if (auth()->user()->hasPermission('members.manage'))
                <a href="{{ route('admin.members.create') }}" class="btn-primary w-full sm:w-auto">+ Tambah Anggota</a>
            @endif
        </x-slot:actions>
    </x-page-header>
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('admin.members.index') }}" class="space-y-4">
            <div><label class="mb-1 block text-xs font-medium text-slate-600">Cari</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama, NIK..." class="input-touch"></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label class="mb-1 block text-xs font-medium text-slate-600">RT</label>
                <select name="rt_id" class="select-touch"><option value="">Semua RT</option>
                @foreach ($rts as $rt)<option value="{{ $rt->id }}" @selected(($filters['rt_id'] ?? '') == $rt->id)>{{ $rt->label() }}</option>@endforeach
                </select></div>
                <div><label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                <select name="status" class="select-touch"><option value="">Semua</option>
                @foreach (\App\Enums\MemberStatus::cases() as $s)<option value="{{ $s->value }}" @selected(($filters['status'] ?? '') === $s->value)>{{ $s->label() }}</option>@endforeach
                </select></div>
    </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="submit" class="btn-primary w-full">Terapkan</button>
                <a href="{{ route('admin.members.index') }}" class="btn-secondary w-full text-center">Reset</a>
        </div>
    </form>
</div>
    <div class="space-y-3 md:hidden">
        @forelse ($members as $member)
        <article class="card p-4">
            <h3 class="font-semibold">{{ $member->nama_lengkap }}</h3>
            <p class="text-sm text-slate-600">{{ $member->rt->label() }} · {{ $member->status->label() }}</p>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="{{ route('admin.members.show', $member) }}" class="btn-secondary justify-center py-2 text-xs text-center">Detail</a>
                @if (auth()->user()->hasPermission('members.manage'))
                <a href="{{ route('admin.members.edit', $member) }}" class="btn-primary justify-center py-2 text-xs text-center">Ubah</a>@endif
            </div>
        </article>
        @empty<p class="card p-6 text-center text-slate-500">Belum ada anggota.</p>@endforelse
</div>
    <div class="card hidden md:block"><<div class="kt-table-wrap">
    <table class="min-w-full text-sm"><thead class="bg-slate-50"><tr>
    <th class="px-4 py-2 text-left">Nama</th><th class="px-4 py-2">RT</th><th class="px-4 py-2">Status</th><th class="px-4 py-2 text-right">Aksi</th>
    </tr></thead><tbody>
    @foreach($members as $member)<tr class="border-t">
    <td class="px-4 py-2 font-medium">{{ $member->nama_lengkap }}</td>
    <td class="px-4 py-2">{{ $member->rt->label() }}</td>
    <td class="px-4 py-2">{{ $member->status->label() }}</td>
    <td class="px-4 py-2 text-right"><a href="{{ route('admin.members.show', $member) }}" class="text-emerald-600">Detail</a></td>
    </tr>@endforeach
    </tbody></table></div></div>
    @if($members->hasPages())<div class="mt-4">{{ $members->links() }}</div>@endif
@endsection