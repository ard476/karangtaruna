<?php

$base = dirname(__DIR__).'/resources/views/layouts';
$t = 'd'.'iv';
$o = '<'.$t;
$c = '</'.$t.'>';

$admin = <<<BLADE
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#059669">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur-md" style="padding-top: env(safe-area-inset-top, 0);">
        {$o} class="mx-auto flex max-w-6xl items-center justify-between gap-2 px-4 py-3">
            {$o} class="min-w-0 flex-1">
                <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600">Panel Pengurus</p>
                <p class="truncate text-sm font-bold text-slate-900">{{ \$organization?->name ?? config('app.name') }}</p>
            {$c}
            {$o} class="hidden shrink-0 items-center gap-2 lg:flex">
                <span class="max-w-[140px] truncate text-xs text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn-danger min-h-9 px-3 py-2 text-xs">Keluar</button></form>
            {$c}
        {$c}
        @include('components.admin-nav-desktop')
    </header>
    <main class="page-main">@include('components.flash')@yield('content')</main>
    @include('components.admin-bottom-nav')
</body>
</html>
BLADE;

$member = <<<BLADE
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#047857">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="sticky top-0 z-40 bg-emerald-700 text-white shadow-md" style="padding-top: env(safe-area-inset-top, 0);">
        {$o} class="mx-auto max-w-4xl px-4 py-3">
            {$o} class="flex items-start justify-between gap-2">
                {$o} class="min-w-0">
                    <p class="text-[10px] text-emerald-200">Portal Anggota</p>
                    <p class="truncate font-semibold">{{ \$organization?->name ?? config('app.name') }}</p>
                {$c}
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">@csrf<button type="submit" class="rounded-lg px-3 py-2 text-xs font-medium text-emerald-100 active:bg-emerald-800">Keluar</button></form>
            {$c}
            <nav class="mt-2 hidden gap-3 md:flex">@include('components.member-nav')</nav>
        {$c}
    </header>
    <main class="page-main-member">@include('components.flash')@yield('content')</main>
    @include('components.member-bottom-nav')
</body>
</html>
BLADE;

$guest = <<<BLADE
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#059669">
    <title>@yield('title', 'Masuk') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 font-sans text-slate-800 antialiased">
    {$o} class="flex min-h-screen flex-col items-center justify-center px-4 py-8" style="padding-top: max(2rem, env(safe-area-inset-top)); padding-bottom: max(2rem, env(safe-area-inset-bottom));">
        {$o} class="mb-6 w-full max-w-md text-center sm:mb-8">
            {$o} class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-600 text-2xl font-bold text-white shadow-lg shadow-emerald-600/30">KT{$c}
            @if (\$organization ?? null)
                <h1 class="text-lg font-semibold text-slate-900">{{ \$organization->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ \$organization->wilayahLabel() }}</p>
            @else
                <h1 class="text-lg font-semibold text-slate-900">{{ config('app.name') }}</h1>
            @endif
        {$c}
        @yield('content')
    {$c}
</body>
</html>
BLADE;

file_put_contents($base.'/admin.blade.php', $admin);
file_put_contents($base.'/member.blade.php', $member);
file_put_contents(dirname($base).'/layouts/guest.blade.php', $guest);
echo "done\n";
