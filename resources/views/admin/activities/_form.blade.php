@php
    use App\Enums\ActivityKind;
    $tipeVal = old('tipe', $activity->tipe?->value ?? ActivityKind::Biasa->value);
    $shiftRows = old('shifts');
    if (! is_array($shiftRows)) {
        if ($activity->relationLoaded('shifts') && $activity->shifts->isNotEmpty()) {
            $shiftRows = $activity->shifts->map(fn ($s) => [
                'id' => (string) $s->id,
                'nama' => $s->nama,
                'mulai_pada' => $s->mulai_pada?->format('Y-m-d\TH:i'),
                'selesai_pada' => $s->selesai_pada?->format('Y-m-d\TH:i'),
                'lokasi' => $s->lokasi,
                'latitude' => $s->latitude,
                'longitude' => $s->longitude,
                'radius_meters' => $s->radius_meters,
                'catatan' => $s->catatan,
            ])->values()->all();
        } else {
            $shiftRows = [[
                'id' => '',
                'nama' => '',
                'mulai_pada' => old('mulai_pada', $activity->mulai_pada?->format('Y-m-d\TH:i')),
                'selesai_pada' => '',
                'lokasi' => '',
                'latitude' => '',
                'longitude' => '',
                'radius_meters' => '',
                'catatan' => '',
            ]];
        }
    }
@endphp
<div class="grid gap-4 sm:grid-cols-2">
<div><label class="text-sm font-medium">Judul *</label><input name="judul" value="{{ old('judul',$activity->judul) }}" required class="input-touch"></div>
<div><label class="text-sm font-medium">Status</label><select name="status" class="input-touch">@foreach(\App\Enums\ActivityStatus::cases() as $s)<option value="{{ $s->value }}" @selected(old('status',$activity->status?->value)==$s->value)>{{ $s->label() }}</option>@endforeach</select></div>
<div><label class="text-sm font-medium">Tipe *</label><select name="tipe" id="activity-tipe" class="input-touch">@foreach(\App\Enums\ActivityKind::cases() as $k)<option value="{{ $k->value }}" @selected($tipeVal === $k->value)>{{ $k->label() }}</option>@endforeach</select></div>
<div class="sm:col-span-2"><p class="text-xs text-slate-500">Hajatan: setiap shift punya jadwal sendiri; absensi wajib foto per shift.</p></div>
<div><label class="text-sm font-medium">Mulai *</label><input type="datetime-local" name="mulai_pada" value="{{ old('mulai_pada',$activity->mulai_pada?->format('Y-m-d\TH:i')) }}" required class="input-touch"></div>
<div><label class="text-sm font-medium">Selesai</label><input type="datetime-local" name="selesai_pada" value="{{ old('selesai_pada',$activity->selesai_pada?->format('Y-m-d\TH:i')) }}" class="input-touch"></div>
<div class="sm:col-span-2"><label class="text-sm font-medium">Lokasi (umum)</label><input name="lokasi" value="{{ old('lokasi',$activity->lokasi) }}" class="input-touch"></div>
</div>
<div><label class="text-sm font-medium">Deskripsi</label><textarea name="deskripsi" rows="4" class="input-touch">{{ old('deskripsi',$activity->deskripsi) }}</textarea></div>

<div id="shifts-block" class="mt-6 space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4 {{ $tipeVal === \App\Enums\ActivityKind::Hajatan->value ? '' : 'hidden' }}">
    <div class="flex items-center justify-between gap-2">
        <h3 class="text-sm font-semibold text-slate-800">Shift &amp; jadwal</h3>
        <button type="button" id="add-shift-row" class="rounded border border-emerald-600 bg-white px-3 py-1.5 text-xs font-medium text-emerald-700">+ Shift</button>
    </div>
    <p class="text-xs text-slate-600">Isi minimal satu shift. Anggota absen per shift dengan foto. Jadwal memakai zona waktu WIB ({{ config('app.timezone') }}).</p>
    <div id="shifts-rows" class="space-y-4">
        @foreach($shiftRows as $i => $row)
            @php
                $row = is_array($row) ? $row : [];
            @endphp
            <div class="shift-row rounded-lg border bg-white p-3 shadow-sm" data-shift-row>
                <input type="hidden" name="shifts[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
                <div class="mb-2 flex justify-end">
                    <button type="button" class="remove-shift text-xs text-red-600" @if(count($shiftRows) < 2) style="display:none" @endif>Hapus baris</button>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label class="text-xs font-medium text-slate-600">Nama shift *</label><input name="shifts[{{ $i }}][nama]" value="{{ $row['nama'] ?? '' }}" class="input-touch mt-0.5 text-sm shift-required" data-shift-required></div>
                    <div><label class="text-xs font-medium text-slate-600">Mulai *</label><input type="datetime-local" name="shifts[{{ $i }}][mulai_pada]" value="{{ $row['mulai_pada'] ?? '' }}" class="input-touch mt-0.5 text-sm shift-required" data-shift-required></div>
                    <div><label class="text-xs font-medium text-slate-600">Selesai</label><input type="datetime-local" name="shifts[{{ $i }}][selesai_pada]" value="{{ $row['selesai_pada'] ?? '' }}" class="input-touch mt-0.5 text-sm"></div>
                    <div class="sm:col-span-2"><label class="text-xs font-medium text-slate-600">Lokasi shift</label><input name="shifts[{{ $i }}][lokasi]" value="{{ $row['lokasi'] ?? '' }}" class="input-touch mt-0.5 text-sm"></div>
                    <div><label class="text-xs font-medium text-slate-600">Latitude titik absen</label><input type="number" step="any" name="shifts[{{ $i }}][latitude]" value="{{ $row['latitude'] ?? '' }}" class="input-touch mt-0.5 text-sm" placeholder="-6.200000"></div>
                    <div><label class="text-xs font-medium text-slate-600">Longitude titik absen</label><input type="number" step="any" name="shifts[{{ $i }}][longitude]" value="{{ $row['longitude'] ?? '' }}" class="input-touch mt-0.5 text-sm" placeholder="106.816666"></div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-medium text-slate-600">Radius absen (meter)</label>
                        <div class="mt-0.5 flex gap-2">
                            <input type="number" min="1" max="10000" name="shifts[{{ $i }}][radius_meters]" value="{{ $row['radius_meters'] ?? '' }}" class="input-touch text-sm" placeholder="Contoh: 100">
                            <button type="button" class="use-current-location rounded border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700">Pakai lokasi saya</button>
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">Kosongkan latitude/longitude/radius jika tidak ingin membatasi radius.</p>
                    </div>
                    <div class="sm:col-span-2"><label class="text-xs font-medium text-slate-600">Catatan</label><input name="shifts[{{ $i }}][catatan]" value="{{ $row['catatan'] ?? '' }}" class="input-touch mt-0.5 text-sm"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
(function () {
    var tipe = document.getElementById('activity-tipe');
    var block = document.getElementById('shifts-block');
    var rowsWrap = document.getElementById('shifts-rows');
    var addBtn = document.getElementById('add-shift-row');
    var form = block ? block.closest('form') : null;
    if (!tipe || !block || !rowsWrap || !addBtn) return;
    var hajatan = '{{ \App\Enums\ActivityKind::Hajatan->value }}';

    function syncShiftFields() {
        var isHajatan = tipe.value === hajatan;
        block.classList.toggle('hidden', !isHajatan);
        block.querySelectorAll('input, select, textarea').forEach(function (inp) {
            if (isHajatan) {
                inp.disabled = false;
                inp.required = inp.hasAttribute('data-shift-required');
            } else {
                inp.required = false;
                inp.disabled = true;
            }
        });
    }

    tipe.addEventListener('change', syncShiftFields);
    syncShiftFields();

    if (form) {
        form.addEventListener('submit', function () {
            if (tipe.value !== hajatan) {
                block.querySelectorAll('input, select, textarea').forEach(function (inp) {
                    inp.disabled = true;
                    inp.required = false;
                });
            }
        });
    }
    function reindex() {
        var rows = rowsWrap.querySelectorAll('[data-shift-row]');
        rows.forEach(function (row, i) {
            row.querySelectorAll('input[name^="shifts["]').forEach(function (inp) {
                inp.name = inp.name.replace(/shifts\[\d+\]/, 'shifts[' + i + ']');
            });
            var rm = row.querySelector('.remove-shift');
            if (rm) rm.style.display = rows.length > 1 ? '' : 'none';
        });
    }
    rowsWrap.addEventListener('click', function (e) {
        if (e.target.closest('.remove-shift')) {
            var row = e.target.closest('[data-shift-row]');
            if (row && rowsWrap.querySelectorAll('[data-shift-row]').length > 1) row.remove();
            reindex();
        }
    });
    addBtn.addEventListener('click', function () {
        var n = rowsWrap.querySelectorAll('[data-shift-row]').length;
        var div = document.createElement('div');
        div.className = 'shift-row rounded-lg border bg-white p-3 shadow-sm';
        div.setAttribute('data-shift-row', '');
        div.innerHTML = '<input type="hidden" name="shifts[' + n + '][id]" value="">' +
            '<div class="mb-2 flex justify-end"><button type="button" class="remove-shift text-xs text-red-600">Hapus baris</button></div>' +
            '<div class="grid gap-3 sm:grid-cols-2">' +
            '<div class="sm:col-span-2"><label class="text-xs font-medium text-slate-600">Nama shift *</label><input name="shifts[' + n + '][nama]" value="" class="input-touch mt-0.5 text-sm shift-required" data-shift-required></div>' +
            '<div><label class="text-xs font-medium text-slate-600">Mulai *</label><input type="datetime-local" name="shifts[' + n + '][mulai_pada]" value="" class="input-touch mt-0.5 text-sm shift-required" data-shift-required></div>' +
            '<div><label class="text-xs font-medium text-slate-600">Selesai</label><input type="datetime-local" name="shifts[' + n + '][selesai_pada]" value="" class="input-touch mt-0.5 text-sm"></div>' +
            '<div class="sm:col-span-2"><label class="text-xs font-medium text-slate-600">Lokasi shift</label><input name="shifts[' + n + '][lokasi]" value="" class="input-touch mt-0.5 text-sm"></div>' +
            '<div><label class="text-xs font-medium text-slate-600">Latitude titik absen</label><input type="number" step="any" name="shifts[' + n + '][latitude]" value="" class="input-touch mt-0.5 text-sm" placeholder="-6.200000"></div>' +
            '<div><label class="text-xs font-medium text-slate-600">Longitude titik absen</label><input type="number" step="any" name="shifts[' + n + '][longitude]" value="" class="input-touch mt-0.5 text-sm" placeholder="106.816666"></div>' +
            '<div class="sm:col-span-2"><label class="text-xs font-medium text-slate-600">Radius absen (meter)</label><div class="mt-0.5 flex gap-2"><input type="number" min="1" max="10000" name="shifts[' + n + '][radius_meters]" value="" class="input-touch text-sm" placeholder="Contoh: 100"><button type="button" class="use-current-location rounded border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700">Pakai lokasi saya</button></div><p class="mt-1 text-[11px] text-slate-500">Kosongkan latitude/longitude/radius jika tidak ingin membatasi radius.</p></div>' +
            '<div class="sm:col-span-2"><label class="text-xs font-medium text-slate-600">Catatan</label><input name="shifts[' + n + '][catatan]" value="" class="input-touch mt-0.5 text-sm"></div>' +
            '</div>';
        rowsWrap.appendChild(div);
        reindex();
        syncShiftFields();
    });
    rowsWrap.addEventListener('click', function (e) {
        var btn = e.target.closest('.use-current-location');
        if (!btn) return;

        var row = btn.closest('[data-shift-row]');
        if (!row || !navigator.geolocation) {
            alert('Browser tidak mendukung geolocation.');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Mengambil...';
        navigator.geolocation.getCurrentPosition(function (position) {
            var lat = row.querySelector('input[name$="[latitude]"]');
            var lng = row.querySelector('input[name$="[longitude]"]');
            if (lat) lat.value = position.coords.latitude.toFixed(7);
            if (lng) lng.value = position.coords.longitude.toFixed(7);
            btn.disabled = false;
            btn.textContent = 'Pakai lokasi saya';
        }, function () {
            alert('Gagal mengambil lokasi. Izinkan akses lokasi di browser.');
            btn.disabled = false;
            btn.textContent = 'Pakai lokasi saya';
        }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
    });
})();
</script>
