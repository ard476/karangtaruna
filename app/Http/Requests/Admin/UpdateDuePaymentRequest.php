<?php

namespace App\Http\Requests\Admin;

use App\Enums\DuePaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDuePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('finance.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(DuePaymentStatus::class)],
            'jumlah_bayar' => ['nullable', 'numeric', 'min:0'],
            'dibayar_pada' => ['nullable', 'date'],
            'metode' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
