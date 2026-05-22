<?php

namespace App\Http\Requests\Admin;

use App\Enums\ActivityKind;
use App\Enums\ActivityStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('activities.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'mulai_pada' => ['required', 'date'],
            'selesai_pada' => ['nullable', 'date', 'after_or_equal:mulai_pada'],
            'status' => ['required', Rule::enum(ActivityStatus::class)],
            'tipe' => ['required', Rule::enum(ActivityKind::class)],
            'shifts' => [
                Rule::when(
                    $this->input('tipe') === ActivityKind::Hajatan->value,
                    ['required', 'array', 'min:1'],
                    ['nullable', 'array']
                ),
            ],
            'shifts.*.id' => ['nullable', 'integer'],
            'shifts.*.nama' => ['required_with:shifts', 'string', 'max:100'],
            'shifts.*.mulai_pada' => ['required_with:shifts', 'date'],
            'shifts.*.selesai_pada' => ['nullable', 'date'],
            'shifts.*.lokasi' => ['nullable', 'string', 'max:255'],
            'shifts.*.latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:shifts.*.radius_meters'],
            'shifts.*.longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:shifts.*.radius_meters'],
            'shifts.*.radius_meters' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'shifts.*.catatan' => ['nullable', 'string', 'max:500'],
        ];
    }
}
