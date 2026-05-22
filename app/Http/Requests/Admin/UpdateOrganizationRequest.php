<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('organization.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'dusun' => ['required', 'string', 'max:100'],
            'rw_number' => ['required', 'string', 'max:5'],
            'rw_name' => ['nullable', 'string', 'max:100'],
            'desa' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'kabupaten' => ['required', 'string', 'max:100'],
            'alamat_lengkap' => ['nullable', 'string', 'max:500'],
            'tahun_berdiri' => ['nullable', 'integer', 'min:1900', 'max:'.date('Y')],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'rts' => ['required', 'array', 'min:1'],
            'rts.*.id' => ['required', 'exists:rts,id'],
            'rts.*.name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
