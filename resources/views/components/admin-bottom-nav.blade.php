@auth
@php
    $user = auth()->user();
    $moreActive = request()->routeIs('admin.dues.*', 'admin.announcements.*', 'admin.reports.*', 'admin.users.*', 'admin.organization.*');
@endphp
<nav class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white/95 pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_20px_rgba(0,0,0,0.06)] backdrop-blur-md lg:hidden" aria-label="Navigasi bawah">
    <div class="mx-auto flex max-w-lg justify-around px-1 pt-1">
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-2 min-h-[56px] {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-slate-500' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-medium leading-tight">Beranda</span>
        </a>
        @if ($user->hasPermission('members.view'))
        <a href="{{ route('admin.members.index') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-2 min-h-[56px] {{ request()->routeIs('admin.members.*') ? 'text-emerald-600' : 'text-slate-500' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-[10px] font-medium">Anggota</span>
        </a>
        @endif
        @if ($user->hasPermission('activities.view'))
        <a href="{{ route('admin.activities.index') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-2 min-h-[56px] {{ request()->routeIs('admin.activities.*') ? 'text-emerald-600' : 'text-slate-500' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-[10px] font-medium">Kegiatan</span>
        </a>
        @endif
        @if ($user->hasPermission('finance.view'))
        <a href="{{ route('admin.transactions.index') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-2 min-h-[56px] {{ request()->routeIs('admin.transactions.*') ? 'text-emerald-600' : 'text-slate-500' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[10px] font-medium">Kas</span>
        </a>
        @endif
        <button type="button" id="admin-menu-toggle" class="flex flex-col items-center justify-center gap-0.5 rounded-xl px-1 py-2 min-h-[56px] w-full {{ $moreActive ? 'text-emerald-600' : 'text-slate-500' }}" aria-expanded="false" aria-controls="admin-menu-sheet">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span class="text-[10px] font-medium">Menu</span>
        </button>
    </div>
</nav>

<div id="admin-menu-backdrop" class="fixed inset-0 z-[60] hidden bg-slate-900/40 lg:hidden" aria-hidden="true"></div>
<div id="admin-menu-sheet" class="fixed inset-x-0 bottom-0 z-[70] max-h-[70vh] translate-y-full rounded-t-2xl border-t border-slate-200 bg-white shadow-2xl transition-transform duration-300 ease-out lg:hidden" role="dialog" aria-modal="true" aria-label="Menu lainnya">
    <div class="flex justify-center pt-3 pb-2"><span class="h-1 w-10 rounded-full bg-slate-300"></span></div>
    <div class="px-4 pb-[calc(1rem+env(safe-area-inset-bottom))]">
        <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Menu lainnya</p>
        <div class="grid gap-1">
            @if ($user->hasPermission('finance.view'))
                <a href="{{ route('admin.dues.index') }}" class="flex min-h-12 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 active:bg-slate-100">Iuran Anggota</a>
            @endif
            @if ($user->hasPermission('announcements.view'))
                <a href="{{ route('admin.announcements.index') }}" class="flex min-h-12 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 active:bg-slate-100">Pengumuman</a>
            @endif
            @if ($user->hasPermission('reports.view'))
                <a href="{{ route('admin.reports.index') }}" class="flex min-h-12 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 active:bg-slate-100">Laporan</a>
            @endif
            @if ($user->hasPermission('users.view'))
                <a href="{{ route('admin.users.index') }}" class="flex min-h-12 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 active:bg-slate-100">Kelola Pengguna</a>
            @endif
            @if ($user->hasPermission('organization.manage'))
                <a href="{{ route('admin.organization.edit') }}" class="flex min-h-12 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 active:bg-slate-100">Profil Organisasi</a>
            @endif
            <div class="my-2 border-t border-slate-100"></div>
            <p class="px-4 text-xs text-slate-500">{{ $user->name }} &middot; {{ $user->roleLabel() }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex min-h-12 w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-medium text-red-600 active:bg-red-50">Keluar</button>
            </form>
        </div>
    </div>
</div>
@endauth
