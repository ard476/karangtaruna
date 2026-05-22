<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('announcements.view'), 403);

        $announcements = Announcement::published()
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('member.announcements.index', compact('announcements'));
    }

    public function show(Request $request, Announcement $announcement): View
    {
        abort_unless($request->user()->hasPermission('announcements.view'), 403);

        if (! $announcement->is_published) {
            abort(404);
        }

        return view('member.announcements.show', compact('announcement'));
    }
}
