@auth
<nav class="hidden items-center gap-1 lg:flex lg:gap-2" aria-label="Navigasi utama">
    <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Dashboard</a>
    @if (auth()->user()->hasPermission('members.view'))
        <a href="{{ route('admin.members.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.members.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Anggota</a>
    @endif
    @if (auth()->user()->hasPermission('activities.view'))
        <a href="{{ route('admin.activities.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.activities.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Kegiatan</a>
    @endif
    @if (auth()->user()->hasPermission('finance.view'))
        <a href="{{ route('admin.transactions.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.transactions.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Keuangan</a>
        <a href="{{ route('admin.dues.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dues.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Iuran</a>
    @endif
    @if (auth()->user()->hasPermission('announcements.view'))
        <a href="{{ route('admin.announcements.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.announcements.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Pengumuman</a>
    @endif
    @if (auth()->user()->hasPermission('reports.view'))
        <a href="{{ route('admin.reports.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.reports.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Laporan</a>
    @endif
    @if (auth()->user()->hasPermission('users.view'))
        <a href="{{ route('admin.users.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Pengguna</a>
    @endif
    @if (auth()->user()->hasPermission('organization.manage'))
        <a href="{{ route('admin.organization.edit') }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.organization.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' }}">Organisasi</a>
    @endif
</nav>
@endauth
