<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">RT <span class="text-red-500">*</span></label>
            <select name="rt_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('rt_id') border-red-500 @enderror">
                <option value="">Pilih RT</option>
                @foreach($rts as $rt)
                    <option value="{{ $rt->id }}" @selected(old('rt_id', $member->rt_id) == $rt->id)>{{ $rt->label() }}</option>
                @endforeach
            </select>
            @error('rt_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
            <select name="status" required class="input-touch">
                @foreach(\App\Enums\MemberStatus::cases() as $s)
                    <option value="{{ $s->value }}" @selected(old('status', $member->status?->value ?? 'aktif') === $s->value)>{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">NIK</label>
            <input type="text" name="nik" value="{{ old('nik', $member->nik) }}" maxlength="16" class="input-touch">
            @error('nik')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $member->nama_lengkap) }}" required class="input-touch">
            @error('nama_lengkap')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="input-touch">
                <option value="">-</option>
                <option value="L" @selected(old('jenis_kelamin', $member->jenis_kelamin) === 'L')>Laki-laki</option>
                <option value="P" @selected(old('jenis_kelamin', $member->jenis_kelamin) === 'P')>Perempuan</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $member->tempat_lahir) }}" class="input-touch">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $member->tanggal_lahir?->format('Y-m-d')) }}" class="input-touch">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" class="input-touch">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $member->email) }}" class="input-touch">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Bergabung Pada</label>
            <input type="date" name="bergabung_pada" value="{{ old('bergabung_pada', $member->bergabung_pada?->format('Y-m-d')) }}" class="input-touch">
        </div>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Alamat</label>
        <textarea name="alamat" rows="2" class="input-touch">{{ old('alamat', $member->alamat) }}</textarea>
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Catatan</label>
        <textarea name="catatan" rows="2" class="input-touch">{{ old('catatan', $member->catatan) }}</textarea>
    </div>

    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <h3 class="mb-3 text-sm font-semibold text-slate-900">Akun Login (opsional)</h3>
        @if($member->hasLogin())
            <div class="space-y-3">
                <p class="text-sm text-slate-600">Akun: <strong>{{ $member->user->username }}</strong></p>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Username</label>
                    <input type="text" name="username" value="{{ old('username', $member->user->username) }}" class="input-touch">
                    @error('username')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Kata Sandi Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="input-touch">
                    <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="user_is_active" value="0">
                    <input type="checkbox" name="user_is_active" value="1" id="user_is_active" @checked(old('user_is_active', $member->user->is_active)) class="rounded border-slate-300 text-emerald-600">
                    <label for="user_is_active" class="text-sm text-slate-700">Akun aktif (bisa login)</label>
                </div>
            </div>
        @else
            <div class="flex items-center gap-2 mb-3">
                <input type="checkbox" name="create_login" value="1" id="create_login" @checked(old('create_login')) class="rounded border-slate-300 text-emerald-600" data-toggle-login>
                <label for="create_login" class="text-sm text-slate-700">Buat akun login untuk anggota ini</label>
            </div>
            <div id="login-fields" class="grid gap-3 sm:grid-cols-2 {{ old('create_login') ? '' : 'hidden' }}">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="input-touch">
                    @error('username')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Kata Sandi</label>
                    <input type="password" name="password" class="input-touch">
                    <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        @endif
    </div>
</div>
<script>
document.querySelector('<divata-toggle-login]')?.addEventListener('change', function() {
    document.getElementById('login-fields')?.classList.toggle('hidden', !this.checked);
});
</script>
