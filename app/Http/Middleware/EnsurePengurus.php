<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePengurus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->canAccessAdmin()) {
            abort(403, 'Halaman ini hanya untuk pengurus.');
        }

        return $next($request);
    }
}
