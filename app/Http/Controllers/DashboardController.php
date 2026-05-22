<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->canAccessAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('member.dashboard');
    }
}
