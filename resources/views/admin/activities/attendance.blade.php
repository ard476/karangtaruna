@extends('layouts.admin')
@section('title','Absensi')
@section('content')
<h1 class="text-2xl font-bold mb-1">Absensi: {{ $activity->judul }}</h1>
<p class="text-sm text-slate-600 mb-4">{{ $activity->mulai_pada->format('d F Y H:i') }}</p>
<div class="rounded-xl border bg-white p-6"><form method="POST" action="{{ route('admin.activities.attendance.update',$activity) }}">@csrf
<table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-3 py-2 text-left">Nama</th><th>RT</th><th>Status</th><th>Keterangan</th></tr></thead><tbody>
@foreach($members as $m)@php $att=$existing->get($m->id);@endphp<tr class="border-t"><td class="px-3 py-2">{{ $m->nama_lengkap }}</td><td class="px-3 py-2">{{ $m->rt->label() }}</td>
<td class="px-3 py-2"><select name="attendance[{{ $m->id }}][status]" class="rounded border text-sm">@foreach(\App\Enums\AttendanceStatus::cases() as $s)<option value="{{ $s->value }}" @selected(old("attendance.{$m->id}.status",$att?->status?->value)==$s->value)>{{ $s->label() }}</option>@endforeach</select></td>
<td class="px-3 py-2"><input name="attendance[{{ $m->id }}][keterangan]" value="{{ old("attendance.{$m->id}.keterangan",$att?->keterangan) }}" class="w-full rounded border text-sm"></td></tr>@endforeach
</tbody></table>
<div class="mt-4 flex gap-2"><button class="rounded bg-emerald-600 px-4 py-2 text-white text-sm">Simpan Absensi</button><a href="{{ route('admin.activities.show',$activity) }}" class="rounded border px-4 py-2 text-sm">Kembali</a></div></form></div>
@endsection