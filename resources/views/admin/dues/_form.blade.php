<div class="grid gap-4 sm:grid-cols-2">
<div><label class="text-sm font-medium">Judul Periode *</label><input name="judul" value="{{ old('judul',$period->judul) }}" required class="input-touch"></div>
<div><label class="text-sm font-medium">Nominal (Rp) *</label><input type="number" name="jumlah" value="{{ old('jumlah',$period->jumlah) }}" min="1" required class="input-touch"></div>
<div><label class="text-sm font-medium">Jatuh Tempo *</label><input type="date" name="jatuh_tempo" value="{{ old('jatuh_tempo',$period->jatuh_tempo?->format('Y-m-d')) }}" required class="input-touch"></div>
<div class="flex items-center gap-2 pt-6"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$period->is_active??true))><label class="text-sm">Periode aktif</label></div>
</div>