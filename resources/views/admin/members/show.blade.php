@extends('layouts.admin')

@section('title', $member->nama_lengkap)

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('admin.members.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">&larr; Kembali</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">{{ $member->nama_lengkap }}</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $member->rt->label() }}</p>
        </div>
        @if (auth()->user()->hasPermission('members.manage'))
            <div class="flex gap-2">
                <a href="{{ route('admin.members.edit', $member) }}"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Ubah</a>
                <form method="POST" action="{{ route('admin.members.destroy', $member) }}"
                    onsubmit="return confirm('Hapus anggota ini? Akun login ikut terhapus.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Hapus</button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Data Pribadi</h2>
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-slate-500">NIK</dt>
                    <dd class="font-medium text-slate-900">{{ $member->nik ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Jenis Kelamin</dt>
                    <dd class="font-medium text-slate-900">
                        @if ($member->jenis_kelamin === 'L')
                            Laki-laki
                        @elseif ($member->jenis_kelamin === 'P')
                            Perempuan
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Tempat, Tanggal Lahir</dt>
                    <dd class="font-medium text-slate-900">
                        {{ $member->tempat_lahir ?? '-' }}{{ $member->tanggal_lahir ? ', '.$member->tanggal_lahir->translatedFormat('d F Y') : '' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Status</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $member->status === \App\Enums\MemberStatus::Aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $member->status->label() }}
                        </span>
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-slate-500">Alamat</dt>
                    <dd class="font-medium text-slate-900">{{ $member->alamat ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Telepon</dt>
                    <dd class="font-medium text-slate-900">{{ $member->phone ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Email</dt>
                    <dd class="font-medium text-slate-900">{{ $member->email ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Bergabung</dt>
                    <dd class="font-medium text-slate-900">{{ $member->bergabung_pada?->translatedFormat('d F Y') ?? '-' }}</dd>
                </div>
            </dl>
            @if ($member->catatan)
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <p class="text-xs font-medium text-slate-500">Catatan</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $member->catatan }}</p>
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Akun Login</h2>
            @if ($member->hasLogin())
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-slate-500">Username</dt>
                        <dd class="font-medium text-slate-900">{{ $member->user->username }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Status Akun</dt>
                        <dd class="font-medium {{ $member->user->is_active ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $member->user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-slate-600">Belum memiliki akun login.</p>
                @if (auth()->user()->hasPermission('members.manage'))
                    <a href="{{ route('admin.members.edit', $member) }}"
                        class="mt-3 inline-block text-sm font-medium text-emerald-600 hover:text-emerald-700">Buat akun login &rarr;</a>
                @endif
            @endif
        </div>
    </div>
@endsection
