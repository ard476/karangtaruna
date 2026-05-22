<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DueController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        $member = $request->user()->member;

        $payments = $member
            ? $member->duePayments()->with('period')->orderByDesc('created_at')->get()
            : collect();

        return view('member.dues.index', compact('payments', 'member'));
    }
}
