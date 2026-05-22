@php
    $labels = array_merge(config('finance.kategori_pemasukan', []), config('finance.kategori_pengeluaran', []));
@endphp
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-medium">Tipe</label>
        <select name="tipe" id="tipe" class="w-full rounded border px-3 py-2 text-sm" required>
            @foreach (\App\Enums\TransactionType::cases() as $tp)
                <option value="{{ $tp->value }}" @selected(old('tipe', $transaction->tipe?->value) == $tp->value)>{{ $tp->label() }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="text-sm font-medium">Tanggal</label>
        <input type="date" name="tanggal" value="{{ old('tanggal', $transaction->tanggal?->format('Y-m-d')) }}" required class="w-full rounded border px-3 py-2 text-sm">
    </div>
    <div>
        <label class="text-sm font-medium">Kategori</label>
        <select name="kategori" id="kategori" class="w-full rounded border px-3 py-2 text-sm" required></select>
    </div>
    <div>
        <label class="text-sm font-medium">Jumlah (Rp)</label>
        <input type="number" name="jumlah" value="{{ old('jumlah', $transaction->jumlah) }}" min="1" required class="w-full rounded border px-3 py-2 text-sm">
    </div>
</div>
<div>
    <label class="text-sm font-medium">Keterangan</label>
    <input name="keterangan" value="{{ old('keterangan', $transaction->keterangan) }}" required class="w-full rounded border px-3 py-2 text-sm">
</div>
<script>
const pemasukan = @json(array_keys(config('finance.kategori_pemasukan')));
const pengeluaran = @json(array_keys(config('finance.kategori_pengeluaran')));
const labels = @json($labels);
const sel = document.getElementById('kategori');
const tipe = document.getElementById('tipe');
let cur = @json(old('kategori', $transaction->kategori));
function fill() {
    const list = tipe.value === 'pengeluaran' ? pengeluaran : pemasukan;
    sel.innerHTML = list.map(k => `<option value="${k}" ${k===cur?'selected':''}>${labels[k]||k}</option>`).join('');
}
tipe.addEventListener('change', () => { cur = ''; fill(); });
fill();
</script>
