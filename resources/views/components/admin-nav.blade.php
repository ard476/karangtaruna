@auth
    <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">Dashboard</a>
    @if (auth()->user()->hasPermission('members.view'))
        <a href="{{ route('admin.members.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.members.*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">Anggota</a>
    @endif
    @if (auth()->user()->hasPermission('activities.view'))
        <a href="{{ route('admin.activities.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.activities.*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">Kegiatan</a>
    @endif
    @if (auth()->user()->hasPermission('finance.view'))
        <a href="{{ route('admin.transactions.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.transactions.*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">Keuangan</a>
        <a href="{{ route('admin.dues.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.dues.*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">Iuran</a>
    @endif
    @if (auth()->user()->hasPermission('announcements.view'))
        <a href="{{ route('admin.announcements.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.announcements.*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">Pengumuman</a>
    @endif
    @if (auth()->user()->hasPermission('reports.view'))
        <a href="{{ route('admin.reports.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.reports.*') ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600' }}">Laporan</a>
    @endif
@endauth