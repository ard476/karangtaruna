<?php

namespace App\Http\Controllers\Webhook;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityShift;
use App\Models\ActivityShiftAttendance;
use App\Models\Member;
use App\Models\WhatsappAttendanceSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WhatsappAttendanceWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->validToken($request)) {
            return $this->reply('Webhook tidak valid.', 403);
        }

        $phone = $this->normalizePhone((string) ($request->input('from') ?? $request->input('phone')));
        if (! $phone) {
            return $this->reply('Nomor WhatsApp tidak terbaca.', 422);
        }

        $member = $this->memberByPhone($phone);
        if (! $member) {
            return $this->reply('Nomor WhatsApp belum terdaftar sebagai anggota. Hubungi pengurus.');
        }

        $type = strtolower((string) $request->input('type', 'text'));
        $text = trim((string) $request->input('text', $request->input('message', '')));

        if ($type === 'text' || $text !== '') {
            if (preg_match('/^absen\s+([a-z0-9-]+)$/i', $text, $match)) {
                return $this->startSession($member, $phone, strtoupper($match[1]));
            }

            if (preg_match('/^(batal|cancel)$/i', $text)) {
                WhatsappAttendanceSession::query()->where('phone', $phone)->delete();

                return $this->reply('Sesi absensi WA dibatalkan.');
            }
        }

        if ($type === 'location' || $request->filled(['latitude', 'longitude'])) {
            return $this->storeLocation($member, $phone, $request);
        }

        if ($type === 'image' || $request->hasFile('photo') || $request->filled(['media_url', 'media_base64'])) {
            return $this->storePhotoAndAttend($member, $phone, $request);
        }

        return $this->reply("Format pesan belum dikenali.\nKetik: ABSEN KODE_SHIFT\nContoh: ABSEN SFT12");
    }

    private function startSession(Member $member, string $phone, string $code): JsonResponse
    {
        $shift = $this->shiftByCode($code);
        if (! $shift || ! $shift->activity?->isHajatan()) {
            return $this->reply('Kode shift tidak ditemukan. Cek lagi kode dari pengurus.');
        }

        if (! $shift->isAssignedTo($member)) {
            return $this->reply('Anda tidak ditugaskan pada shift ini.');
        }

        WhatsappAttendanceSession::updateOrCreate(
            [
                'activity_shift_id' => $shift->id,
                'member_id' => $member->id,
            ],
            [
                'phone' => $phone,
                'latitude' => null,
                'longitude' => null,
                'distance_meters' => null,
                'expires_at' => now()->addMinutes(30),
            ]
        );

        $message = "Sesi absen dibuka untuk {$shift->activity->judul} - {$shift->nama}.\n";
        $message .= $shift->hasRadius()
            ? "Kirim lokasi/current location WhatsApp dulu, lalu kirim foto."
            : "Kirim foto absensi. Jika bisa, kirim lokasi juga sebelum foto.";
        $message .= "\nKetik BATAL untuk membatalkan.";

        return $this->reply($message);
    }

    private function storeLocation(Member $member, string $phone, Request $request): JsonResponse
    {
        $session = $this->activeSession($member, $phone);
        if (! $session) {
            return $this->reply('Tidak ada sesi absen aktif. Ketik: ABSEN KODE_SHIFT');
        }

        $latitude = (float) $request->input('latitude');
        $longitude = (float) $request->input('longitude');

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return $this->reply('Koordinat lokasi tidak valid.');
        }

        $shift = $session->shift;
        $distance = null;
        if ($shift->hasRadius()) {
            $distance = $shift->distanceTo($latitude, $longitude);
            if ($distance > $shift->radius_meters) {
                return $this->reply("Lokasi di luar radius. Jarak Anda {$distance} meter, radius shift {$shift->radius_meters} meter.");
            }
        }

        $session->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance_meters' => $distance,
            'expires_at' => now()->addMinutes(30),
        ]);

        return $this->reply($distance === null
            ? 'Lokasi tersimpan. Sekarang kirim foto absensi.'
            : "Lokasi valid ({$distance} meter dari titik absen). Sekarang kirim foto absensi.");
    }

    private function storePhotoAndAttend(Member $member, string $phone, Request $request): JsonResponse
    {
        $session = $this->activeSession($member, $phone);
        if (! $session) {
            return $this->reply('Tidak ada sesi absen aktif. Ketik: ABSEN KODE_SHIFT');
        }

        $shift = $session->shift;
        if ($shift->hasRadius() && ($session->latitude === null || $session->longitude === null)) {
            return $this->reply('Shift ini memakai radius. Kirim lokasi/current location WhatsApp dulu sebelum foto.');
        }

        $photoPath = $this->storeIncomingPhoto($request, $shift);
        if (! $photoPath) {
            return $this->reply('Foto tidak terbaca. Kirim ulang foto absensi.', 422);
        }

        $existing = ActivityShiftAttendance::query()
            ->where('activity_shift_id', $shift->id)
            ->where('member_id', $member->id)
            ->first();

        if ($existing?->photo_path) {
            Storage::disk('public')->delete($existing->photo_path);
        }

        ActivityShiftAttendance::updateOrCreate(
            [
                'activity_shift_id' => $shift->id,
                'member_id' => $member->id,
            ],
            [
                'status' => AttendanceStatus::Hadir,
                'photo_path' => $photoPath,
                'absen_latitude' => $session->latitude,
                'absen_longitude' => $session->longitude,
                'distance_meters' => $session->distance_meters,
                'keterangan' => 'Absensi via WhatsApp',
                'absen_pada' => now(),
                'recorded_by' => null,
            ]
        );

        $session->delete();

        return $this->reply("Absensi berhasil disimpan.\n{$shift->activity->judul} - {$shift->nama}");
    }

    private function activeSession(Member $member, string $phone): ?WhatsappAttendanceSession
    {
        return WhatsappAttendanceSession::query()
            ->with('shift.activity')
            ->where('member_id', $member->id)
            ->where('phone', $phone)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    private function shiftByCode(string $code): ?ActivityShift
    {
        if (! preg_match('/^SFT(\d+)$/i', $code, $match)) {
            return null;
        }

        return ActivityShift::query()->with('activity')->find((int) $match[1]);
    }

    private function storeIncomingPhoto(Request $request, ActivityShift $shift): ?string
    {
        $directory = "absensi-wa/{$shift->activity_id}/{$shift->id}";

        if ($request->hasFile('photo')) {
            return $request->file('photo')->store($directory, 'public');
        }

        if ($request->filled('media_base64')) {
            $content = base64_decode((string) $request->input('media_base64'), true);
            if ($content === false) {
                return null;
            }

            $path = $directory.'/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($path, $content);

            return $path;
        }

        if ($request->filled('media_url')) {
            $response = Http::timeout(20)->get((string) $request->input('media_url'));
            if (! $response->successful()) {
                return null;
            }

            $path = $directory.'/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($path, $response->body());

            return $path;
        }

        return null;
    }

    private function memberByPhone(string $phone): ?Member
    {
        return Member::query()
            ->whereNotNull('phone')
            ->get()
            ->first(fn (Member $member) => $this->normalizePhone((string) $member->phone) === $phone);
    }

    private function normalizePhone(string $phone): ?string
    {
        $number = preg_replace('/\D+/', '', $phone);
        if (! $number) {
            return null;
        }

        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return $number;
    }

    private function validToken(Request $request): bool
    {
        $token = config('services.whatsapp.webhook_token');
        if (! $token) {
            return true;
        }

        return hash_equals($token, (string) ($request->bearerToken() ?: $request->input('token')));
    }

    private function reply(string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'reply' => $message,
            'message' => $message,
        ], $status);
    }
}
