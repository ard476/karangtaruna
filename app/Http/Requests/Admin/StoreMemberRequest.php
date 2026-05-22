<?php

namespace App\Http\Requests\Admin;

use App\Enums\MemberStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('members.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'rt_id' => ['required', 'exists:rts,id'],
            'nik' => ['nullable', 'digits:16', 'unique:members,nik'],
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
                Rule::requiredIf(fn () => $this->boolean('create_login')),
                'nullable',
                'string',
                'max:50',
                'alpha_dash',
                'unique:users,username',
            ],
            'password' => [
                Rule::requiredIf(fn () => $this->boolean('create_login')),
                'nullable',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus 16 digit.',
            'username.required' => 'Username wajib diisi jika membuat akun login.',
            'password.required' => 'Kata sandi wajib diisi jika membuat akun login.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'create_login' => $this->boolean('create_login'),
        ]);
    }
}
