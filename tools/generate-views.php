<?php

/** Generate blade views using __D__ / __/D__ placeholders */
$base = dirname(__DIR__).'/resources/views';

$files = [];

$files['components/admin-nav.blade.php'] = <<<'BLADE'
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
BLADE;

$files['components/member-nav.blade.php'] = <<<'BLADE'
<a href="{{ route('member.dashboard') }}" class="text-sm {{ request()->routeIs('member.dashboard') ? 'text-white font-semibold' : 'text-emerald-100 hover:text-white' }}">Beranda</a>
<a href="{{ route('member.activities.index') }}" class="text-sm {{ request()->routeIs('member.activities.*') ? 'text-white font-semibold' : 'text-emerald-100 hover:text-white' }}">Kegiatan</a>
<a href="{{ route('member.announcements.index') }}" class="text-sm {{ request()->routeIs('member.announcements.*') ? 'text-white font-semibold' : 'text-emerald-100 hover:text-white' }}">Pengumuman</a>
<a href="{{ route('member.dues.index') }}" class="text-sm {{ request()->routeIs('member.dues.*') ? 'text-white font-semibold' : 'text-emerald-100 hover:text-white' }}">Iuran</a>
BLADE;

$files['layouts/admin.blade.php'] = <<<'BLADE'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <nav class="border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
            <__D__ class="flex flex-wrap items-center gap-4">
                <__D__>
                    <p class="text-xs font-medium uppercase tracking-wide text-emerald-600">Panel Pengurus</p>
                    <p class="font-semibold text-slate-900">{{ $organization?->name ?? config('app.name') }}</p>
                __/D__
                @include('components.admin-nav')
            __/D__
            <__D__ class="flex items-center gap-4">
                <span class="hidden text-sm text-slate-600 sm:inline">{{ auth()->user()->name }} ({{ auth()->user()->roleLabel() }})</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Keluar</button></form>
            __/D__
        __/D__
    </nav>
    <main class="mx-auto max-w-6xl px-4 py-8">
        @if (session('success'))<__D__ class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</__/D__>@endif
        @yield('content')
    </main>
</body>
</html>
BLADE;

$files['layouts/member.blade.php'] = <<<'BLADE'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <nav class="bg-emerald-700 text-white shadow-md">
        <__D__ class="mx-auto max-w-4xl px-4 py-4">
            <__D__ class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <__D__>
                    <p class="text-xs text-emerald-200">Portal Anggota</p>
                    <p class="font-semibold">{{ $organization?->name ?? config('app.name') }}</p>
                __/D__
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-sm text-emerald-100 hover:text-white">Keluar</button></form>
            __/D__
            <__D__ class="flex flex-wrap gap-4">@include('components.member-nav')</__/D__
        __/D__
    </nav>
    <main class="mx-auto max-w-4xl px-4 py-8">
        @if (session('success'))<__D__ class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</__/D__>@endif
        @yield('content')
    </main>
</body>
</html>
BLADE;

function convert(string $content): string
{
    $t = 'd'.'iv';

    return str_replace(['__D__', '__/D__'], ['<'.$t, '</'.$t.'>'], $content);
}

foreach ($files as $path => $content) {
    $full = $base.'/'.$path;
    $dir = dirname($full);
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($full, convert($content));
}

echo "Generated ".count($files)." base views.\n";
