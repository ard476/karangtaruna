@extends('layouts.member')
@section('title', 'Absen ' . $shift->nama)
@section('content')
<a href="{{ route('member.activities.show', $activity) }}" class="text-sm text-emerald-600">&larr; Kembali</a>
<h1 class="text-xl font-bold mt-2">{{ $shift->nama }}</h1>
<p class="text-sm text-slate-600">{{ $activity->judul }}</p>
<p class="text-sm text-slate-500 mb-4">
    {{ $shift->mulai_pada->format('d F Y, H:i') }}
    @if($shift->selesai_pada) &mdash; {{ $shift->selesai_pada->format('H:i') }} @endif
    @if($shift->lokasi)<br>Lokasi: {{ $shift->lokasi }}@endif
    @if($shift->hasRadius())
        <br>Radius absen: {{ $shift->radius_meters }} meter
    @endif
</p>

@if($attendance?->status === \App\Enums\AttendanceStatus::Hadir && $attendance->photo_path)
    <div class="rounded-xl border bg-emerald-50 p-4 mb-4 text-sm">
        <p class="font-medium text-emerald-800">Anda sudah absen shift ini</p>
        <p class="text-emerald-700 mt-1">{{ $attendance->absen_pada?->format('d/m/Y H:i') ?? '—' }}</p>
        <a href="{{ $attendance->photoUrl() }}" target="_blank" rel="noopener" class="inline-block mt-3">
            <img src="{{ $attendance->photoUrl() }}" alt="Foto absen" class="h-32 w-32 rounded-lg border object-cover">
        </a>
        <p class="text-xs text-slate-600 mt-3">Isi ulang form di bawah untuk mengganti foto absensi.</p>
    </div>
@endif

<div class="rounded-xl border bg-white p-5">
    <form method="POST" action="{{ route('member.activities.shift-absen.store', [$activity, $shift]) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="hidden" name="status" value="hadir">
        <input type="hidden" name="latitude" id="attendance-latitude" value="{{ old('latitude') }}">
        <input type="hidden" name="longitude" id="attendance-longitude" value="{{ old('longitude') }}">
        <input type="hidden" name="accuracy" id="attendance-accuracy" value="{{ old('accuracy') }}">
        <input type="hidden" id="location-bypass" value="0">
        @if($shift->hasRadius())
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                <p class="font-medium">Lokasi wajib aktif</p>
                <p class="mt-1 text-xs">Anda harus berada maksimal {{ $shift->radius_meters }} meter dari titik absen shift ini.</p>
                @if(app()->environment('local') && config('app.attendance_allow_http_without_location'))
                    <p class="mt-1 text-xs">Mode testing HTTP aktif: jika lokasi diblokir browser, absensi tetap bisa dikirim tanpa cek radius.</p>
                @endif
                <p id="location-status" class="mt-2 text-xs">Mengambil lokasi HP...</p>
                @error('latitude')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        @else
            <p id="location-status" class="text-xs text-slate-500">Lokasi akan disimpan jika Anda mengizinkan akses lokasi.</p>
        @endif
        <div>
            <label class="text-sm font-medium text-slate-700">Foto absensi *</label>
            <p class="text-xs text-slate-500 mb-2">Foto wajib diambil langsung dari kamera HP.</p>
            <input type="file" name="photo" id="attendance-photo" accept="image/*" capture="environment" class="hidden">
            <div class="overflow-hidden rounded-lg border bg-slate-950">
                <video id="camera-preview" class="aspect-video w-full object-cover" autoplay muted playsinline></video>
                <canvas id="camera-canvas" class="hidden"></canvas>
            </div>
            <button type="button" id="capture-photo" class="mt-3 w-full rounded-lg border border-emerald-600 bg-white px-3 py-2.5 text-sm font-medium text-emerald-700" disabled>
                Ambil foto dari kamera
            </button>
            <button type="button" id="fallback-camera" class="mt-3 hidden w-full rounded-lg border border-emerald-600 bg-white px-3 py-2.5 text-sm font-medium text-emerald-700">
                Buka kamera HP
            </button>
            <p id="camera-status" class="mt-2 text-xs text-slate-500">Membuka kamera...</p>
            <img id="captured-photo-preview" alt="Preview foto absensi" class="mt-3 hidden h-32 w-32 rounded-lg border object-cover">
            @error('photo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="text-sm font-medium text-slate-700">Keterangan (opsional)</label>
            <input type="text" name="keterangan" value="{{ old('keterangan', $attendance?->keterangan) }}" maxlength="255" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <button type="submit" id="submit-attendance" class="btn-primary w-full" disabled>Kirim absensi</button>
    </form>
</div>
<script>
(function () {
    var lat = document.getElementById('attendance-latitude');
    var lng = document.getElementById('attendance-longitude');
    var acc = document.getElementById('attendance-accuracy');
    var status = document.getElementById('location-status');
    var submit = document.getElementById('submit-attendance');
    var form = submit ? submit.closest('form') : null;
    var locationBypass = document.getElementById('location-bypass');
    var photoInput = document.getElementById('attendance-photo');
    var video = document.getElementById('camera-preview');
    var canvas = document.getElementById('camera-canvas');
    var capture = document.getElementById('capture-photo');
    var fallbackCamera = document.getElementById('fallback-camera');
    var cameraStatus = document.getElementById('camera-status');
    var capturedPreview = document.getElementById('captured-photo-preview');
    var requiresRadius = @json($shift->hasRadius());
    var allowHttpLocationBypass = @json(app()->environment('local') && config('app.attendance_allow_http_without_location'));
    var locationReady = !requiresRadius;
    var photoReady = false;

    function setStatus(message, isError) {
        if (!status) return;
        status.textContent = message;
        status.classList.toggle('text-red-600', !!isError);
        status.classList.toggle('text-emerald-700', !isError);
    }

    function setCameraStatus(message, isError) {
        if (!cameraStatus) return;
        cameraStatus.textContent = message;
        cameraStatus.classList.toggle('text-red-600', !!isError);
        cameraStatus.classList.toggle('text-emerald-700', !isError);
    }

    function refreshSubmitState() {
        if (submit) submit.disabled = !(photoReady && locationReady);
    }

    function showFallbackCamera(message) {
        if (capture) capture.classList.add('hidden');
        if (video) video.classList.add('hidden');
        if (fallbackCamera) fallbackCamera.classList.remove('hidden');
        setCameraStatus(message, true);
    }

    if (fallbackCamera) {
        fallbackCamera.addEventListener('click', function () {
            if (photoInput) photoInput.click();
        });
    }

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            var file = photoInput.files && photoInput.files[0];
            if (!file) {
                photoReady = false;
                refreshSubmitState();
                return;
            }

            capturedPreview.src = URL.createObjectURL(file);
            capturedPreview.classList.remove('hidden');
            photoReady = true;
            setCameraStatus('Foto sudah dipilih dari kamera HP.', false);
            refreshSubmitState();
        });
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            if (!photoReady || !photoInput.files.length) {
                event.preventDefault();
                setCameraStatus('Ambil foto dari kamera dulu sebelum mengirim absensi.', true);
            }
        });
    }

    if (!navigator.geolocation) {
        if (requiresRadius && allowHttpLocationBypass) {
            locationReady = true;
            if (locationBypass) locationBypass.value = '1';
            setStatus('Lokasi diblokir/tidak didukung. Mode testing HTTP aktif, absensi tetap bisa dikirim tanpa cek radius.', true);
            refreshSubmitState();
        } else {
            setStatus('Browser tidak mendukung lokasi. Gunakan Chrome/Safari terbaru.', true);
        }
    } else {
        navigator.geolocation.getCurrentPosition(function (position) {
            lat.value = position.coords.latitude.toFixed(7);
            lng.value = position.coords.longitude.toFixed(7);
            acc.value = Math.round(position.coords.accuracy || 0);
            locationReady = true;
            if (locationBypass) locationBypass.value = '0';
            setStatus('Lokasi didapat (akurasi sekitar ' + acc.value + ' meter).', false);
            refreshSubmitState();
        }, function () {
            if (requiresRadius && allowHttpLocationBypass) {
                locationReady = true;
                if (locationBypass) locationBypass.value = '1';
                setStatus('Lokasi diblokir karena HTTP. Mode testing HTTP aktif, absensi tetap bisa dikirim tanpa cek radius.', true);
            } else {
                setStatus('Gagal mengambil lokasi. Izinkan akses lokasi, lalu refresh halaman.', requiresRadius);
                locationReady = !requiresRadius;
            }
            refreshSubmitState();
        }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        showFallbackCamera('Kamera langsung diblokir browser. Tekan "Buka kamera HP" untuk mengambil foto.');
        refreshSubmitState();
        return;
    }

    navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: 'environment' } },
        audio: false
    }).then(function (stream) {
        video.srcObject = stream;
        if (capture) capture.disabled = false;
        setCameraStatus('Kamera aktif. Tekan tombol "Ambil foto dari kamera".', false);
    }).catch(function () {
        showFallbackCamera('Kamera langsung tidak bisa dibuka. Tekan "Buka kamera HP" untuk mengambil foto.');
    });

    if (capture) {
        capture.addEventListener('click', function () {
            if (!video.videoWidth || !video.videoHeight) {
                setCameraStatus('Kamera belum siap. Tunggu sebentar lalu coba lagi.', true);
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(function (blob) {
                if (!blob) {
                    setCameraStatus('Gagal mengambil foto. Coba ulangi.', true);
                    return;
                }

                var file = new File([blob], 'absensi-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                photoInput.files = dataTransfer.files;

                capturedPreview.src = URL.createObjectURL(file);
                capturedPreview.classList.remove('hidden');
                photoReady = true;
                setCameraStatus('Foto sudah diambil. Ulangi jika ingin mengganti foto.', false);
                refreshSubmitState();
            }, 'image/jpeg', 0.9);
        });
    }

    refreshSubmitState();
})();
</script>
@endsection
