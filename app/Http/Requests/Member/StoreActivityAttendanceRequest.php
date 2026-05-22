<?php

namespace App\Http\Requests\Member;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('activities.view') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([AttendanceStatus::Hadir->value, AttendanceStatus::Izin->value])],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
