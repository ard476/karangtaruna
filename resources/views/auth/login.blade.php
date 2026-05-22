@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="w-full max-w-md card p-6 sm:p-8">
        <h2 class="mb-6 text-center text-xl font-semibold text-slate-900">Masuk ke Sistem</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                    autocomplete="username" class="input-touch">
            </div>
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Kata Sandi</label>
                <input type="password" name="password" id="password" required autocomplete="current-password"
                    class="input-touch">
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="remember" id="remember"
                    class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="remember" class="text-sm text-slate-600">Ingat saya</label>
            </div>
            <button type="submit" class="btn-primary w-full">
                Masuk
            </button>
        </form>
    </div>
@endsection
