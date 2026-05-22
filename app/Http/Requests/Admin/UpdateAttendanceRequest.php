<?php

namespace App\Http\Requests\Admin;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('activities.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', Rule::enum(AttendanceStatus::class)],
            'attendance.*.keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
