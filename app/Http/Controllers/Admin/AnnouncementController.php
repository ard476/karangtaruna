<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Http\Requests\Admin\UpdateAnnouncementRequest;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('announcements.view'), 403);

        $announcements = Announcement::query()
            ->filter($request->only(['published']))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.announcements.index', [
            'announcements' => $announcements,
            'filters' => $request->only(['published']),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasPermission('announcements.manage'), 403);

        return view('admin.announcements.create', ['announcement' => new Announcement]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        if ($data['is_published'] ?? false) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(Request $request, Announcement $announcement): View
    {
        abort_unless($request->user()->hasPermission('announcements.view'), 403);

        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Request $request, Announcement $announcement): View
    {
        abort_unless($request->user()->hasPermission('announcements.manage'), 403);

        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validated();

        if (($data['is_published'] ?? false) && ! $announcement->published_at) {
            $data['published_at'] = now();
        }

        if (! ($data['is_published'] ?? false)) {
            $data['published_at'] = null;
        }

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Request $request, Announcement $announcement): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('announcements.manage'), 403);

        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
