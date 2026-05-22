<?php

namespace App\Http\Requests\Member;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShiftAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('activities.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([AttendanceStatus::Hadir->value, AttendanceStatus::Izin->value])],
            'photo' => ['required_if:status,'.AttendanceStatus::Hadir->value, 'image', 'max:5120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
