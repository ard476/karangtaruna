<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShiftAssignmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('activities.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', Rule::exists('members', 'id')],
        ];
    }
}
