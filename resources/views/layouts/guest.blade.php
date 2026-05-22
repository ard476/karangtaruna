<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#059669">
    <title>@yield('title', 'Masuk') - {{ config('app.name') }}</title>
    @include('components.assets-head')
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50 font-sans text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-8" style="padding-top: max(2rem, env(safe-area-inset-top)); padding-bottom: max(2rem, env(safe-area-inset-bottom));">
        <div class="mb-6 w-full max-w-md text-center sm:mb-8">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-600 text-2xl font-bold text-white shadow-lg shadow-emerald-600/30">KT</div>
            @if ($organization ?? null)
                <h1 class="text-lg font-semibold text-slate-900">{{ $organization->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $organization->wilayahLabel() }}</p>
            @else
                <h1 class="text-lg font-semibold text-slate-900">{{ config('app.name') }}</h1>
            @endif
        </div>
        @yield('content')
    </div>
</body>
</html>