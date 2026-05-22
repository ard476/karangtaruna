<div class="grid gap-4 sm:grid-cols-2">
<div><label class="text-sm font-medium">Nama *</label>
<input name="name" value="{{ old('name', $user->name) }}" required class="input-touch"></</div>
<div><label class="text-sm font-medium">Username *</label>
<input name="username" value="{{ old('username', $user->username) }}" required class="input-touch"></</div>
<div><label class="text-sm font-medium">Email</label>
<input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-touch"></</div>
<div><label class="text-sm font-medium">Telepon</label>
<input name="phone" value="{{ old('phone', $user->phone) }}" class="input-touch"></</div>
<div><label class="text-sm font-medium">Peran *</label>
<select name="role" required class="select-touch">
@foreach($roles as $role)
<option value="{{ $role->value }}" @selected(old('role', $user->role?->value) === $role->value)>{{ $role->label() }}</option>
@endforeach
</select></</div>
<div class="flex items-center gap-2 sm:col-span-2">
<input type="hidden" name="is_active" value="0">
<input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $user->is_active ?? true)) class="h-5 w-5 rounded">
<label for="is_active" class="text-sm">Akun aktif</label></</div>
</div>
<div><label class="text-sm font-medium">Kata Sandi {{ isset($user->id) ? '(kosongkan jika tidak diubah)' : '*' }}</label>
<input type="password" name="password" class="input-touch" {{ isset($user->id) ? '' : 'required' }}>
<input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" class="input-touch mt-2"></</div>