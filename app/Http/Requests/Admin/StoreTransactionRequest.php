<?php

namespace App\Http\Requests\Admin;

use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('finance.manage') ?? false;
    }

    public function rules(): array
    {
        $tipe = $this->input('tipe');
        $categories = $tipe === TransactionType::Pengeluaran->value
            ? array_keys(config('finance.kategori_pengeluaran', []))
            : array_keys(config('finance.kategori_pemasukan', []));

        return [
            'tipe' => ['required', Rule::enum(TransactionType::class)],
            'kategori' => ['required', 'string', Rule::in($categories)],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['required', 'string', 'max:500'],
            'tanggal' => ['required', 'date'],
        ];
    }
}
