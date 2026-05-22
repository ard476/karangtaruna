@echo off
echo Membuka port 8001 di Windows Firewall...
echo Jalankan file ini dengan klik kanan ^> Run as administrator
echo.
netsh advfirewall firewall add rule name="Laravel Karang Taruna 8001" dir=in action=allow protocol=TCP localport=8001
if %errorlevel%==0 (
    echo.
    echo Berhasil. Coba buka di HP: http://192.168.31.110:8001
) else (
    echo.
    echo Gagal. Pastikan dijalankan sebagai Administrator.
)
pause
