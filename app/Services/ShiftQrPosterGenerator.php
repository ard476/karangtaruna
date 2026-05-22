<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityShift;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use GdImage;
use RuntimeException;

class ShiftQrPosterGenerator
{
    public function generateQrPng(string $url, int $targetSize = 520): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => false,
            'scale' => 12,
        ]);

        $png = (new QRCode($options))->render($url);
        $qr = imagecreatefromstring($png);

        if (! $qr instanceof GdImage) {
            throw new RuntimeException('Gagal membuat gambar QR.');
        }

        $sourceWidth = imagesx($qr);
        $sourceHeight = imagesy($qr);
        $canvas = imagecreatetruecolor($targetSize, $targetSize);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopyresampled($canvas, $qr, 0, 0, 0, 0, $targetSize, $targetSize, $sourceWidth, $sourceHeight);

        ob_start();
        imagepng($canvas);
        $output = (string) ob_get_clean();

        imagedestroy($qr);
        imagedestroy($canvas);

        return $output;
    }

    public function generatePoster(
        Activity $activity,
        ActivityShift $shift,
        string $organizationName,
        string $attendanceUrl,
        ?string $logoPath = null,
    ): string {
        $qrPng = $this->generateQrPng($attendanceUrl);
        $qr = imagecreatefromstring($qrPng);

        if (! $qr instanceof GdImage) {
            throw new RuntimeException('Gagal memuat gambar QR.');
        }

        $width = 900;
        $height = 1200;
        $img = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($img, 240, 253, 244);
        $card = imagecolorallocate($img, 255, 255, 255);
        $emerald = imagecolorallocate($img, 5, 150, 105);
        $title = imagecolorallocate($img, 15, 23, 42);
        $muted = imagecolorallocate($img, 100, 116, 139);
        $border = imagecolorallocate($img, 209, 250, 229);

        imagefill($img, 0, 0, $bg);
        imagefilledrectangle($img, 45, 45, $width - 45, $height - 45, $card);

        $font = $this->fontPath();
        $y = 120;

        if ($logoPath && is_file($logoPath)) {
            $this->drawLogo($img, $logoPath, (int) (($width - 120) / 2), 85, 120);
            $y = 250;
        }

        if ($font) {
            $y = $this->drawCenteredText($img, $font, 22, $emerald, 'ABSENSI QR SHIFT', $width / 2, $y, 760);
            $y = $this->drawCenteredText($img, $font, 30, $title, $organizationName, $width / 2, $y + 36, 760, 2);
            $y = $this->drawCenteredText($img, $font, 22, $title, $activity->judul, $width / 2, $y + 24, 760, 3);
            $y = $this->drawCenteredText($img, $font, 24, $title, 'Shift: '.$shift->nama, $width / 2, $y + 24, 760, 2);

            $schedule = $shift->mulai_pada->format('d/m/Y H:i');
            if ($shift->selesai_pada) {
                $schedule .= ' - '.$shift->selesai_pada->format('H:i');
            }

            $y = $this->drawCenteredText($img, $font, 18, $muted, $schedule, $width / 2, $y + 28, 760, 2);
            $this->drawCenteredText($img, $font, 20, $title, 'Scan untuk absen tanpa login', $width / 2, 1060, 760);
            $this->drawCenteredText($img, $font, 14, $muted, $attendanceUrl, $width / 2, 1110, 760, 3);
        } else {
            imagestring($img, 5, 300, 100, 'ABSENSI QR SHIFT', $emerald);
            imagestring($img, 5, 180, 170, $this->truncate($organizationName, 38), $title);
            imagestring($img, 4, 180, 220, $this->truncate($activity->judul, 42), $title);
            imagestring($img, 4, 180, 260, $this->truncate('Shift: '.$shift->nama, 42), $title);
            imagestring($img, 3, 180, 300, 'Scan untuk absen tanpa login', $title);
        }

        imagefilledrectangle($img, 155, 455, 745, 1045, $card);
        imagerectangle($img, 155, 455, 745, 1045, $border);
        imagecopyresampled($img, $qr, 190, 490, 0, 0, 520, 520, imagesx($qr), imagesy($qr));

        ob_start();
        imagepng($img);
        $output = (string) ob_get_clean();

        imagedestroy($qr);
        imagedestroy($img);

        return $output;
    }

    public function downloadFilename(Activity $activity, ActivityShift $shift, string $organizationName): string
    {
        $slug = static fn (string $value): string => trim((string) preg_replace('/[^a-z0-9]+/i', '-', $value), '-');

        return strtolower($slug($organizationName.'-'.$activity->judul.'-'.$shift->nama)).'.png';
    }

    private function drawLogo(GdImage $canvas, string $path, int $x, int $y, int $size): void
    {
        $contents = @file_get_contents($path);
        if ($contents === false) {
            return;
        }

        $logo = @imagecreatefromstring($contents);
        if (! $logo instanceof GdImage) {
            return;
        }

        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, $x - 8, $y - 8, $x + $size + 8, $y + $size + 8, $white);
        imagecopyresampled($canvas, $logo, $x, $y, 0, 0, $size, $size, imagesx($logo), imagesy($logo));
        imagedestroy($logo);
    }

    private function fontPath(): ?string
    {
        $candidates = [
            resource_path('fonts/DejaVuSans.ttf'),
            'C:\\Windows\\Fonts\\arial.ttf',
            'C:\\Windows\\Fonts\\segoeui.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/dejavu/DejaVuSans.ttf',
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function wrapText(string $font, float $size, string $text, int $maxWidth, int $maxLines = 10): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $line = '';

        foreach ($words as $word) {
            $test = $line === '' ? $word : $line.' '.$word;
            $box = imagettfbbox($size, 0, $font, $test);
            $lineWidth = abs($box[2] - $box[0]);

            if ($lineWidth > $maxWidth && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $test;
            }
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        return array_slice($lines, 0, $maxLines);
    }

    private function drawCenteredText(
        GdImage $img,
        string $font,
        float $size,
        int $color,
        string $text,
        int $centerX,
        int $startY,
        int $maxWidth,
        int $maxLines = 10,
    ): int {
        $lines = $this->wrapText($font, $size, $text, $maxWidth, $maxLines);
        $lineHeight = (int) round($size * 1.45);
        $y = $startY;

        foreach ($lines as $line) {
            $box = imagettfbbox($size, 0, $font, $line);
            $textWidth = abs($box[2] - $box[0]);
            $x = (int) ($centerX - ($textWidth / 2));
            imagettftext($img, $size, 0, $x, $y, $color, $font, $line);
            $y += $lineHeight;
        }

        return $y;
    }

    private function truncate(string $text, int $length): string
    {
        return mb_strlen($text) > $length
            ? mb_substr($text, 0, $length - 1).'…'
            : $text;
    }
}
