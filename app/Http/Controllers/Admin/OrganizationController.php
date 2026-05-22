<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Models\Rt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()->hasPermission('organization.manage'), 403);

        $organization = Organization::with('rts')->firstOrFail();

        return view('admin.organization.edit', compact('organization'));
    }

    public function update(UpdateOrganizationRequest $request): RedirectResponse
    {
        $organization = Organization::firstOrFail();

        DB::transaction(function () use ($request, $organization) {
            $organization->update($request->safe()->except('rts'));

            foreach ($request->validated('rts') as $rtData) {
                Rt::where('id', $rtData['id'])
                    ->where('organization_id', $organization->id)
                    ->update(['name' => $rtData['name'] ?? null]);
            }
        });

        return redirect()
            ->route('admin.organization.edit')
            ->with('success', 'Profil organisasi berhasil diperbarui.');
    }
}
