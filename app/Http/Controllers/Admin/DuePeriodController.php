<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DuePaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDuePeriodRequest;
use App\Http\Requests\Admin\UpdateDuePaymentRequest;
use App\Http\Requests\Admin\UpdateDuePeriodRequest;
use App\Models\DuePayment;
use App\Models\DuePeriod;
use App\Services\DuePeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DuePeriodController extends Controller
{
    public function __construct(private DuePeriodService $dueService) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        $periods = DuePeriod::query()
            ->withCount(['payments as lunas_count' => fn ($q) => $q->where('status', DuePaymentStatus::Lunas)])
            ->withCount('payments')
            ->orderByDesc('jatuh_tempo')
            ->paginate(12);

        return view('admin.dues.index', compact('periods'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->hasPermission('finance.manage'), 403);

        return view('admin.dues.create', ['period' => new DuePeriod(['jatuh_tempo' => now()->addDays(7)->toDateString()])]);
    }

    public function store(StoreDuePeriodRequest $request): RedirectResponse
    {
        $period = DB::transaction(function () use ($request) {
            $period = DuePeriod::create([
                ...$request->validated(),
                'created_by' => $request->user()->id,
            ]);
            $this->dueService->generatePaymentsForActiveMembers($period);

            return $period;
        });

        return redirect()->route('admin.dues.show', $period)
            ->with('success', 'Periode iuran dibuat. Tagihan anggota aktif sudah digenerate.');
    }

    public function show(Request $request, DuePeriod $period): View
    {
        abort_unless($request->user()->hasPermission('finance.view'), 403);

        $payments = $period->payments()
            ->with('member.rt')
            ->join('members', 'members.id', '=', 'due_payments.member_id')
            ->orderBy('members.nama_lengkap')
            ->select('due_payments.*')
            ->get();

        return view('admin.dues.show', ['period' => $period, 'payments' => $payments]);
    }

    public function edit(Request $request, DuePeriod $period): View
    {
        abort_unless($request->user()->hasPermission('finance.manage'), 403);

        return view('admin.dues.edit', ['period' => $period]);
    }

    public function update(UpdateDuePeriodRequest $request, DuePeriod $period): RedirectResponse
    {
        $period->update($request->validated());

        return redirect()->route('admin.dues.show', $period)
            ->with('success', 'Periode iuran diperbarui.');
    }

    public function destroy(Request $request, DuePeriod $period): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('finance.manage'), 403);

        $period->delete();

        return redirect()->route('admin.dues.index')
            ->with('success', 'Periode iuran dihapus.');
    }

    public function updatePayment(UpdateDuePaymentRequest $request, DuePeriod $period, DuePayment $payment): RedirectResponse
    {
        abort_if($payment->due_period_id !== $period->id, 404);

        $data = $request->validated();

        if ($data['status'] === DuePaymentStatus::Lunas->value) {
            $data['jumlah_bayar'] = $data['jumlah_bayar'] ?? $period->jumlah;
            $data['dibayar_pada'] = $data['dibayar_pada'] ?? now()->toDateString();
            $data['recorded_by'] = $request->user()->id;
        } else {
            $data['jumlah_bayar'] = null;
            $data['dibayar_pada'] = null;
        }

        $payment->update($data);

        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}
