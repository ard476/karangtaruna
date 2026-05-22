<nav class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white/95 pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_20px_rgba(0,0,0,0.06)] backdrop-blur-md md:hidden" aria-label="Navigasi bawah">
    <div class="mx-auto grid max-w-lg grid-cols-4">
        <a href="{{ route('member.dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 min-h-[56px] {{ request()->routeIs('member.dashboard') ? 'text-emerald-600' : 'text-slate-500' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <a href="{{ route('member.activities.index') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 min-h-[56px] {{ request()->routeIs('member.activities.*') ? 'text-emerald-600' : 'text-slate-500' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="text-[10px] font-medium">Kegiatan</span>
        </a>
        <a href="{{ route('member.announcements.index') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 min-h-[56px] {{ request()->routeIs('member.announcements.*') ? 'text-emerald-600' : 'text-slate-500' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            <span class="text-[10px] font-medium">Info</span>
        </a>
        <a href="{{ route('member.dues.index') }}" class="flex flex-col items-center justify-center gap-0.5 py-2 min-h-[56px] {{ request()->routeIs('member.dues.*') ? 'text-emerald-600' : 'text-slate-500' }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span class="text-[10px] font-medium">Iuran</span>
        </a>
    </div>
</nav>
