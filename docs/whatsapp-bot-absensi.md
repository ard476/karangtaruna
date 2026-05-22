# Dokumentasi Webhook WhatsApp Bot Absensi

Dokumen ini menjelaskan cara membuat bot WhatsApp custom untuk absensi kegiatan hajatan. Bot bertugas menerima pesan WhatsApp dari anggota, mengirimkannya ke aplikasi Karang Taruna, lalu membalas anggota memakai isi `reply` dari response webhook.

## Endpoint

```text
POST /webhook/whatsapp/absensi
```

Contoh URL lokal:

```text
http://192.168.200.140:8003/webhook/whatsapp/absensi
```

Untuk internet/produksi, endpoint harus bisa diakses publik, misalnya melalui domain HTTPS atau tunnel seperti ngrok.

## Token Keamanan

Untuk sementara bot **tidak wajib memakai token**.

Biarkan `.env` kosong:

```env
WHATSAPP_WEBHOOK_TOKEN=
```

Jika suatu saat ingin mengaktifkan token, isi `WHATSAPP_WEBHOOK_TOKEN`. Setelah token diisi, bot harus mengirim token lewat header:

```http
Authorization: Bearer isi-token-rahasia
```

atau lewat field body:

```json
{
  "token": "isi-token-rahasia"
}
```

## Konsep Alur

1. Admin membuat kegiatan hajatan.
2. Admin membuat shift dan menugaskan anggota ke shift.
3. Admin melihat kode WA di detail kegiatan, contoh `SFT12`.
4. Anggota mengirim pesan ke bot:

```text
ABSEN SFT12
```

5. Bot meneruskan pesan tersebut ke webhook.
6. Aplikasi mengecek nomor WA, shift, dan penugasan anggota.
7. Aplikasi membalas instruksi lewat JSON `reply`.
8. Bot mengirim isi `reply` ke WhatsApp anggota.
9. Jika shift punya radius, anggota wajib kirim lokasi WhatsApp.
10. Anggota kirim foto absensi.
11. Aplikasi menyimpan absensi sebagai `Hadir` dengan keterangan `Absensi via WhatsApp`.

## Identifikasi Anggota

Webhook mencocokkan nomor WhatsApp dari field `from` atau `phone` dengan kolom `members.phone`.

Nomor dinormalisasi otomatis:

```text
081234567890 -> 6281234567890
+62 812-3456-7890 -> 6281234567890
```

Pastikan nomor HP anggota di data anggota sesuai dengan nomor WhatsApp yang dipakai.

## Kode Shift

Kode shift memakai format:

```text
SFT{id_shift}
```

Contoh:

```text
SFT12
```

Kode ini tampil di halaman detail kegiatan admin pada setiap shift.

## Format Request

Webhook menerima JSON, multipart form-data, atau field biasa. Response selalu JSON.

### 1. Pesan Teks: Mulai Absensi

Request dari bot:

```http
POST /webhook/whatsapp/absensi
Content-Type: application/json
```

```json
{
  "from": "6281234567890",
  "type": "text",
  "text": "ABSEN SFT12"
}
```

Response sukses:

```json
{
  "reply": "Sesi absen dibuka untuk Hajatan Pak RT - Shift Pagi.\nKirim lokasi/current location WhatsApp dulu, lalu kirim foto.\nKetik BATAL untuk membatalkan.",
  "message": "Sesi absen dibuka untuk Hajatan Pak RT - Shift Pagi.\nKirim lokasi/current location WhatsApp dulu, lalu kirim foto.\nKetik BATAL untuk membatalkan."
}
```

Bot harus mengirim nilai `reply` ke anggota.

### 2. Pesan Teks: Batalkan Sesi

```json
{
  "from": "6281234567890",
  "type": "text",
  "text": "BATAL"
}
```

Response:

```json
{
  "reply": "Sesi absensi WA dibatalkan.",
  "message": "Sesi absensi WA dibatalkan."
}
```

Kata `CANCEL` juga diterima.

### 3. Kirim Lokasi

Dipakai untuk shift yang memakai radius. Untuk shift tanpa radius, lokasi tetap boleh dikirim dan akan disimpan di sesi.

```json
{
  "from": "6281234567890",
  "type": "location",
  "latitude": -6.200000,
  "longitude": 106.816666
}
```

Response jika valid:

```json
{
  "reply": "Lokasi valid (42 meter dari titik absen). Sekarang kirim foto absensi.",
  "message": "Lokasi valid (42 meter dari titik absen). Sekarang kirim foto absensi."
}
```

Response jika di luar radius:

```json
{
  "reply": "Lokasi di luar radius. Jarak Anda 250 meter, radius shift 100 meter.",
  "message": "Lokasi di luar radius. Jarak Anda 250 meter, radius shift 100 meter."
}
```

### 4. Kirim Foto via URL Media

Jika provider bot menyediakan URL media:

```json
{
  "from": "6281234567890",
  "type": "image",
  "media_url": "https://provider-wa.com/media/foto.jpg"
}
```

Response sukses:

```json
{
  "reply": "Absensi berhasil disimpan.\nHajatan Pak RT - Shift Pagi",
  "message": "Absensi berhasil disimpan.\nHajatan Pak RT - Shift Pagi"
}
```

### 5. Kirim Foto via Base64

Jika bot mengirim file sebagai base64:

```json
{
  "from": "6281234567890",
  "type": "image",
  "media_base64": "/9j/4AAQSkZJRgABAQAAAQABAAD..."
}
```

### 6. Kirim Foto via Multipart

Jika bot mengirim file langsung:

```text
POST /webhook/whatsapp/absensi
Content-Type: multipart/form-data

from=6281234567890
type=image
photo=<file>
```

## Response Error Umum

Nomor tidak terbaca:

```json
{
  "reply": "Nomor WhatsApp tidak terbaca.",
  "message": "Nomor WhatsApp tidak terbaca."
}
```

Nomor belum terdaftar:

```json
{
  "reply": "Nomor WhatsApp belum terdaftar sebagai anggota. Hubungi pengurus.",
  "message": "Nomor WhatsApp belum terdaftar sebagai anggota. Hubungi pengurus."
}
```

Kode shift salah:

```json
{
  "reply": "Kode shift tidak ditemukan. Cek lagi kode dari pengurus.",
  "message": "Kode shift tidak ditemukan. Cek lagi kode dari pengurus."
}
```

Anggota bukan petugas shift:

```json
{
  "reply": "Anda tidak ditugaskan pada shift ini.",
  "message": "Anda tidak ditugaskan pada shift ini."
}
```

Foto dikirim sebelum sesi:

```json
{
  "reply": "Tidak ada sesi absen aktif. Ketik: ABSEN KODE_SHIFT",
  "message": "Tidak ada sesi absen aktif. Ketik: ABSEN KODE_SHIFT"
}
```

Shift radius tapi foto dikirim sebelum lokasi:

```json
{
  "reply": "Shift ini memakai radius. Kirim lokasi/current location WhatsApp dulu sebelum foto.",
  "message": "Shift ini memakai radius. Kirim lokasi/current location WhatsApp dulu sebelum foto."
}
```

## Session Absensi

Setelah anggota mengetik `ABSEN SFT{id}`, sistem membuat sesi absensi WA selama 30 menit.

Sesi menyimpan:

- anggota,
- shift,
- nomor WA,
- lokasi jika dikirim,
- jarak dari titik absen jika shift memakai radius,
- waktu kadaluarsa.

Setelah foto berhasil disimpan, sesi dihapus.

Jika anggota mengirim `BATAL`, semua sesi aktif untuk nomor tersebut dihapus.

## Catatan Radius

Jika shift memiliki:

- `latitude`
- `longitude`
- `radius_meters`

maka lokasi wajib dikirim sebelum foto.

Jika shift tidak memiliki radius, anggota boleh langsung kirim foto setelah `ABSEN SFT{id}`.

## Data Absensi yang Disimpan

Jika sukses, sistem membuat/memperbarui record `activity_shift_attendances`:

```text
status = hadir
photo_path = path foto dari WA
absen_latitude = latitude sesi, jika ada
absen_longitude = longitude sesi, jika ada
distance_meters = jarak dari titik shift, jika ada
keterangan = Absensi via WhatsApp
absen_pada = waktu saat foto diterima
recorded_by = null
```

Jika anggota absen ulang via WA, foto lama dihapus dan diganti foto baru.

## Pseudocode Bot

```js
async function onIncomingWhatsAppMessage(message) {
  const payload = mapMessageToWebhookPayload(message);

  const response = await fetch('https://domain-anda.com/webhook/whatsapp/absensi', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload),
  });

  const data = await response.json();

  await sendWhatsAppMessage(message.from, data.reply || data.message || 'Tidak ada balasan.');
}

function mapMessageToWebhookPayload(message) {
  if (message.type === 'text') {
    return {
      from: message.from,
      type: 'text',
      text: message.text,
    };
  }

  if (message.type === 'location') {
    return {
      from: message.from,
      type: 'location',
      latitude: message.latitude,
      longitude: message.longitude,
    };
  }

  if (message.type === 'image') {
    return {
      from: message.from,
      type: 'image',
      media_url: message.mediaUrl,
    };
  }
}
```

## Contoh Bot Node.js Sederhana

Contoh ini hanya menunjukkan cara meneruskan payload ke aplikasi. Bagian menerima pesan dan mengirim balasan tergantung library/provider WA yang dipakai.

```js
const WEBHOOK_URL = 'https://domain-anda.com/webhook/whatsapp/absensi';

async function forwardToKarangTaruna(payload) {
  const res = await fetch(WEBHOOK_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(payload),
  });

  const data = await res.json();
  return data.reply || data.message || 'Tidak ada balasan dari server.';
}

async function handleText(from, text) {
  return forwardToKarangTaruna({
    from,
    type: 'text',
    text,
  });
}

async function handleLocation(from, latitude, longitude) {
  return forwardToKarangTaruna({
    from,
    type: 'location',
    latitude,
    longitude,
  });
}

async function handleImage(from, mediaUrl) {
  return forwardToKarangTaruna({
    from,
    type: 'image',
    media_url: mediaUrl,
  });
}
```

## Testing dengan cURL

Mulai sesi:

```bash
curl -X POST "http://localhost:8003/webhook/whatsapp/absensi" \
  -H "Content-Type: application/json" \
  -d '{"from":"6281234567890","type":"text","text":"ABSEN SFT12"}'
```

Kirim lokasi:

```bash
curl -X POST "http://localhost:8003/webhook/whatsapp/absensi" \
  -H "Content-Type: application/json" \
  -d '{"from":"6281234567890","type":"location","latitude":-6.2,"longitude":106.8}'
```

Kirim foto via URL:

```bash
curl -X POST "http://localhost:8003/webhook/whatsapp/absensi" \
  -H "Content-Type: application/json" \
  -d '{"from":"6281234567890","type":"image","media_url":"https://example.com/foto.jpg"}'
```

## Checklist Bot Custom

- Terima event pesan masuk dari WhatsApp.
- Ambil nomor pengirim dan kirim sebagai `from`.
- Jika pesan teks, kirim `type=text` dan `text`.
- Jika lokasi, kirim `type=location`, `latitude`, `longitude`.
- Jika foto, kirim `type=image` dan salah satu: `media_url`, `media_base64`, atau multipart `photo`.
- Panggil endpoint webhook.
- Ambil `reply` dari response.
- Kirim `reply` kembali ke WhatsApp anggota.
- Simpan log request/response di sisi bot untuk debugging.

## Catatan Produksi

- Gunakan HTTPS untuk webhook.
- `WHATSAPP_WEBHOOK_TOKEN` boleh dikosongkan. Isi hanya jika nanti ingin mengaktifkan token.
- Pastikan nomor anggota di database memakai nomor WhatsApp aktif.
- Pastikan provider bot bisa mengirim lokasi dan foto.
- Jika provider `media_url` butuh authorization khusus, bot sebaiknya download media terlebih dahulu lalu kirim `media_base64` atau multipart `photo`.
