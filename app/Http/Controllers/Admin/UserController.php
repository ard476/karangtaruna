<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('users.view'), 403);

        $users = User::query()
            ->when($request->role, fn ($q, $role) => $q->where('role', $role))
            ->when($request->q, function ($q, $search) {
                $term = '%'.$search.'%';
                $q->where(fn ($inner) => $inner
                    ->where('name', 'ilike', $term)
                    ->orWhere('username', 'ilike', $term));
            })
            ->orderByRaw("CASE WHEN role = 'superadmin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $request->only(['q', 'role']),
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('users.manage'), 403);

        return view('admin.users.create', [
            'user' => new User(['is_active' => true]),
            'roles' => UserRole::assignable(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            ...$request->validated(),
            'email' => $request->email ?? $request->username.'@karangtaruna.local',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        abort_unless(auth()->user()->hasPermission('users.manage'), 403);
        abort_if($user->isSuperAdmin() && ! auth()->user()->isSuperAdmin(), 403);

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::assignable(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Akun super admin tidak dapat diubah dari sini.');

        $data = $request->safe()->except('password');

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if ($user->id === auth()->id()) {
            $data['is_active'] = true;
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('users.manage'), 403);
        abort_if($user->isSuperAdmin(), 403);
        abort_if($user->id === auth()->id(), 403, 'Tidak dapat menghapus akun sendiri.');

        if ($user->member) {
            $user->member->update(['user_id' => null]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
