<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTransactionRequest;
use App\Http\Requests\Admin\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\FinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private FinanceService $finance) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        $filters = $request->only(['tipe', 'kategori', 'dari', 'sampai']);

        $transactions = Transaction::query()
            ->filter($filters)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'saldo' => $this->finance->saldo(),
            'totalMasuk' => $this->finance->totalPemasukan($filters['dari'] ?? null, $filters['sampai'] ?? null),
            'totalKeluar' => $this->finance->totalPengeluaran($filters['dari'] ?? null, $filters['sampai'] ?? null),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasPermission('finance.manage'), 403);

        return view('admin.transactions.create', [
            'transaction' => new Transaction(['tanggal' => now()->toDateString(), 'tipe' => TransactionType::Pemasukan]),
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        Transaction::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil dicatat.');
    }

    public function edit(Request $request, Transaction $transaction): View
    {
        abort_unless($request->user()->hasPermission('finance.manage'), 403);

        return view('admin.transactions.edit', compact('transaction'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $transaction->update($request->validated());

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('finance.manage'), 403);

        $transaction->delete();

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
