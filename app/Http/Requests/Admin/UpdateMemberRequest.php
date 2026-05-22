<?php

namespace App\Http\Requests\Admin;

use App\Enums\MemberStatus;
use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('members.manage') ?? false;
    }

    public function rules(): array
    {
        /** @var Member $member */
        $member = $this->route('member');

        return [
            'rt_id' => ['required', 'exists:rts,id'],
            'nik' => [
                'nullable',
                'digits:16',
                Rule::unique('members', 'nik')->ignore($member->id),
            ],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'alamat' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::enum(MemberStatus::class)],
            'bergabung_pada' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'create_login' => ['sometimes', 'boolean'],
            'username' => [
                Rule::requiredIf(fn () => $this->boolean('create_login') && ! $member->hasLogin()),
                'nullable',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($member->user_id),
            ],
            'password' => [
                Rule::requiredIf(fn () => $this->boolean('create_login') && ! $member->hasLogin()),
                'nullable',
                'confirmed',
                Password::min(8),
            ],
            'user_is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus 16 digit.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'create_login' => $this->boolean('create_login'),
            'user_is_active' => $this->boolean('user_is_active'),
        ]);
    }
}
