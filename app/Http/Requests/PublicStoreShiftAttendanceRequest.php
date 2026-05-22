<?php

namespace App\Http\Requests;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PublicStoreShiftAttendanceRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_lengkap' => trim((string) $this->input('nama_lengkap')),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in([AttendanceStatus::Hadir->value])],
            'photo' => ['required', 'image', 'max:5120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Isi nama anggota terlebih dahulu.',
            'nama_lengkap.max' => 'Nama anggota maksimal 255 karakter.',
            'photo.required' => 'Foto absensi wajib diambil dari kamera.',
        ];
    }
}
