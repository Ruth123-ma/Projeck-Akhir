@echo off
cd /d "%~dp0"

set DB_NAME=db_stock_gudang

echo [stock-gudang] Membuat database jika belum ada...
mysql -u root -e "CREATE DATABASE IF NOT EXISTS %DB_NAME%;"

echo [stock-gudang] Menjalankan migrasi dan seed otomatis...
php artisan migrate --seed

echo [stock-gudang] Menjalankan aplikasi di http://127.0.0.1:8000
php artisan serve --host=127.0.0.1 --port=8000
