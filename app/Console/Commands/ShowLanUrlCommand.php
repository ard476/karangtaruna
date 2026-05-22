<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowLanUrlCommand extends Command
{
    protected $signature = 'app:lan {--port=8003 : Port server Laravel}';

    protected $description = 'Tampilkan alamat untuk membuka aplikasi dari HP (WiFi sama)';

    public function handle(): int
    {
        $port = (int) $this->option('port');

        $this->info('Buka di browser HP (WiFi sama dengan PC ini):');
        $this->newLine();

        foreach ($this->lanAddresses() as $ip) {
            $this->line("  <fg=green;options=bold>http://{$ip}:{$port}</>");
        }

        if ($this->lanAddresses() === []) {
            $this->warn('  IP WiFi tidak terdeteksi. Cek koneksi WiFi lalu jalankan: ipconfig');
        }

        $this->newLine();
        $this->comment('Langkah:');
        $this->line('  1. Jalankan server: php artisan serve --host=0.0.0.0 --port='.$port);
        $this->line('     (atau double-click file serve-mobile.bat)');
        $this->line('  2. Buka firewall port '.$port.' (PowerShell Admin):');
        $this->line('     netsh advfirewall firewall add rule name="Laravel '.$port.'" dir=in action=allow protocol=TCP localport='.$port);
        $this->line('  3. HP harus satu jaringan WiFi dengan PC (bukan data seluler)');
        $this->line('  4. Jangan pakai IP 172.x (itu virtual/WSL, biasanya tidak bisa dari HP)');
        $this->newLine();
        $this->line('CSS/JS: sudah di-build → tidak perlu npm run dev saat tes di HP.');
        $this->line('Jika ubah tampilan: npm run build lalu refresh HP.');

        $primary = $this->primaryLanIp();
        if ($primary) {
            $this->newLine();
            $this->line('Opsional di .env (stabil untuk foto & redirect):');
            $this->line("  APP_URL=http://{$primary}:{$port}");
        }

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function lanAddresses(): array
    {
        $ips = [];

        $primary = $this->primaryLanIp();
        if ($primary) {
            $ips[] = $primary;
        }

        $hostname = gethostname();
        if ($hostname) {
            $resolved = gethostbynamel($hostname) ?: [];
            foreach ($resolved as $ip) {
                if ($this->isPrivateLanIp($ip)) {
                    $ips[] = $ip;
                }
            }
        }

        $ips = array_values(array_unique($ips));

        $wifiIps = array_values(array_filter($ips, fn (string $ip) => $this->isWifiLanIp($ip)));

        return $wifiIps !== [] ? $wifiIps : $ips;
    }

    private function isWifiLanIp(string $ip): bool
    {
        return str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.');
    }

    private function primaryLanIp(): ?string
    {
        if (! function_exists('socket_create')) {
            return null;
        }

        $sock = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($sock === false) {
            return null;
        }

        @socket_connect($sock, '8.8.8.8', 53);
        $ok = @socket_getsockname($sock, $addr);
        @socket_close($sock);

        if (! $ok || ! is_string($addr) || ! $this->isPrivateLanIp($addr)) {
            return null;
        }

        return $addr;
    }

    private function isPrivateLanIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false
            && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }
}
