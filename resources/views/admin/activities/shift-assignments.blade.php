@extends('layouts.admin')
@section('title', 'Petugas Shift')
@section('content')
<a href="{{ route('admin.activities.show', $activity) }}" class="text-sm text-emerald-600">&larr; Kembali ke kegiatan</a>
<h1 class="text-2xl font-bold mt-2">Petugas: {{ $shift->nama }}</h1>
<p class="text-sm text-slate-600 mb-1">{{ $activity->judul }}</p>
<p class="text-sm text-slate-500 mb-4">
    {{ $shift->mulai_pada->format('d F Y, H:i') }}
    @if($shift->selesai_pada) &mdash; {{ $shift->selesai_pada->format('d/m/Y H:i') }} @endif
</p>

<div class="rounded-xl border bg-white p-6">
    <form method="POST" action="{{ route('admin.activities.shift.assignments.update', [$activity, $shift]) }}">
        @csrf
        <p class="text-sm text-slate-600 mb-4">Centang anggota yang bertugas di shift ini. Hanya anggota terpilih yang bisa absen shift ini di portal anggota.</p>

        <div class="mb-4 flex flex-wrap gap-2">
            <button type="button" id="select-all-members" class="rounded border px-3 py-1.5 text-xs">Pilih semua</button>
            <button type="button" id="clear-all-members" class="rounded border px-3 py-1.5 text-xs">Kosongkan</button>
        </div>

        <div class="max-h-[60vh] overflow-y-auto space-y-2 border rounded-lg p-3">
            @foreach($members as $m)
                <label class="flex items-center gap-3 rounded-lg border border-slate-100 px-3 py-2.5 hover:bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="member_ids[]" value="{{ $m->id }}" class="h-4 w-4 rounded border-slate-300 text-emerald-600"
                        @checked(in_array($m->id, old('member_ids', $assignedIds)))>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-medium text-slate-900">{{ $m->nama_lengkap }}</span>
                        <span class="block text-xs text-slate-500">{{ $m->rt->label() }}</span>
                    </span>
                </label>
            @endforeach
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <button type="submit" class="rounded bg-emerald-600 px-4 py-2 text-sm text-white">Simpan petugas</button>
            <a href="{{ route('admin.activities.show', $activity) }}" class="rounded border px-4 py-2 text-sm">Batal</a>
        </div>
    </form>
</div>

<script>
(function () {
    var boxes = document.querySelectorAll('input[name="member_ids[]"]');
    var selectAll = document.getElementById('select-all-members');
    var clearAll = document.getElementById('clear-all-members');
    if (selectAll) selectAll.addEventListener('click', function () { boxes.forEach(function (b) { b.checked = true; }); });
    if (clearAll) clearAll.addEventListener('click', function () { boxes.forEach(function (b) { b.checked = false; }); });
})();
</script>
@endsection
