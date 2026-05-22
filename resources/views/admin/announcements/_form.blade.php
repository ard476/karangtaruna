<div class="space-y-4">
<div><label class="text-sm font-medium">Judul *</label><input name="judul" value="{{ old('judul',$announcement->judul) }}" required class="input-touch"></div>
<div><label class="text-sm font-medium">Isi *</label><textarea name="isi" rows="8" required class="input-touch">{{ old('isi',$announcement->isi) }}</textarea></div>
<div class="flex items-center gap-2"><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$announcement->is_published))><label class="text-sm">Terbitkan sekarang</label></div>
<div><label class="text-sm font-medium">Kadaluarsa (opsional)</label><input type="datetime-local" name="expires_at" value="{{ old('expires_at',$announcement->expires_at?->format('Y-m-d\TH:i')) }}" class="input-touch"></div>
</div>