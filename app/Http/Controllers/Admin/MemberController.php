<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MemberStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMemberRequest;
use App\Http\Requests\Admin\UpdateMemberRequest;
use App\Models\Member;
use App\Models\Rt;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('members.view'), 403);

        $members = Member::query()
            ->with(['rt', 'user'])
            ->filter($request->only(['rt_id', 'status', 'q']))
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        return view('admin.members.index', [
            'members' => $members,
            'rts' => Rt::orderBy('number')->get(),
            'filters' => $request->only(['rt_id', 'status', 'q']),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasPermission('members.manage'), 403);

        return view('admin.members.create', [
            'member' => new Member(['status' => MemberStatus::Aktif]),
            'rts' => Rt::orderBy('number')->get(),
        ]);
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $member = Member::create($this->memberAttributes($data));

            if ($data['create_login'] ?? false) {
                $user = $this->createUserForMember($data, $member);
                $member->update(['user_id' => $user->id]);
            }
        });

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Request $request, Member $member): View
    {
        abort_unless($request->user()->hasPermission('members.view'), 403);

        $member->load(['rt', 'user']);

        return view('admin.members.show', compact('member'));
    }

    public function edit(Request $request, Member $member): View
    {
        abort_unless($request->user()->hasPermission('members.manage'), 403);

        $member->load('user');

        return view('admin.members.edit', [
            'member' => $member,
            'rts' => Rt::orderBy('number')->get(),
        ]);
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $member) {
            $member->update($this->memberAttributes($data));

            if ($member->hasLogin()) {
                $member->user->update([
                    'name' => $data['nama_lengkap'],
                    'phone' => $data['phone'] ?? null,
                    'is_active' => $data['user_is_active'] ?? $member->user->is_active,
                ]);

                if (! empty($data['username'])) {
                    $member->user->update(['username' => $data['username']]);
                }

                if (! empty($data['password'])) {
                    $member->user->update(['password' => $data['password']]);
                }
            } elseif ($data['create_login'] ?? false) {
                $user = $this->createUserForMember($data, $member);
                $member->update(['user_id' => $user->id]);
            }
        });

        return redirect()
            ->route('admin.members.show', $member)
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Request $request, Member $member): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('members.manage'), 403);

        DB::transaction(function () use ($member) {
            if ($member->user) {
                $member->user->delete();
            }
            $member->delete();
        });

        return redirect()
            ->route('admin.members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function memberAttributes(array $data): array
    {
        return collect($data)->only([
            'rt_id',
            'nik',
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'alamat',
            'phone',
            'email',
            'status',
            'bergabung_pada',
            'catatan',
        ])->toArray();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function createUserForMember(array $data, Member $member): User
    {
        return User::create([
            'name' => $data['nama_lengkap'],
            'username' => $data['username'],
            'email' => $data['email'] ?? $data['username'].'@karangtaruna.local',
            'password' => $data['password'],
            'role' => UserRole::Anggota,
            'is_active' => true,
            'phone' => $data['phone'] ?? null,
        ]);
    }
}
