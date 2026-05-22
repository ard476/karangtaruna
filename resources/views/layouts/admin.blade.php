<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#059669">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @include('components.assets-head')
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur-md" style="padding-top: env(safe-area-inset-top, 0);">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-2 px-4 py-3">
            <div class="flex min-w-0 flex-1 items-center gap-3">
                @if($organization?->logoUrl())
                    <img src="{{ $organization->logoUrl() }}" alt="Logo {{ $organization->name }}" class="h-10 w-10 shrink-0 rounded-xl border bg-white object-contain p-1">
                @endif
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-600">Panel Pengurus</p>
                    <p class="truncate text-sm font-bold text-slate-900">{{ $organization?->name ?? config('app.name') }}</p>
                </div>
            </div>
            <div class="hidden shrink-0 items-center gap-2 lg:flex">
                <span class="max-w-[140px] truncate text-xs text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn-danger min-h-9 px-3 py-2 text-xs">Keluar</button></form>
            </div>
        </div>
        @include('components.admin-nav-desktop')
    </header>
    <main class="page-main">@include('components.flash')@yield('content')</main>
    @include('components.admin-bottom-nav')
    @include('components.assets-footer')
</body>
</html>