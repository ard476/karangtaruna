<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#047857">
    <title>@yield('title', 'Beranda') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="sticky top-0 z-40 bg-emerald-700 text-white shadow-md" style="padding-top: env(safe-area-inset-top, 0);">
        <div class="mx-auto max-w-4xl px-4 py-3">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-[10px] text-emerald-200">Portal Anggota</p>
                    <p class="truncate font-semibold">{{ $organization?->name ?? config('app.name') }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">@csrf<button type="submit" class="rounded-lg px-3 py-2 text-xs font-medium text-emerald-100 active:bg-emerald-800">Keluar</button></form>
            </div>
            <nav class="mt-2 hidden gap-3 md:flex">@include('components.member-nav')</nav>
        </div>
    </header>
    <main class="page-main-member">@include('components.flash')@yield('content')</main>
    @include('components.member-bottom-nav')
</body>
</html>