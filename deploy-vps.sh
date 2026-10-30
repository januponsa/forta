#!/usr/bin/env bash
set -e

echo "===================================================="
echo "  FORTA VPS AUTO-UPDATE & DATABASE SYNC SCRIPT"
echo "===================================================="

echo "[1/6] Menarik kode terbaru dari GitHub (origin/main)..."
git fetch origin main
git reset --hard origin/main

echo "[2/6] Memastikan dependensi Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "[3/6] Mengimpor database terbaru (forta_db.sql) termasuk seluruh data dummy & akun dosen..."
php -r '
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$host = config("database.connections.mysql.host", "127.0.0.1");
$port = config("database.connections.mysql.port", "3306");
$db   = config("database.connections.mysql.database");
$user = config("database.connections.mysql.username");
$pass = config("database.connections.mysql.password");

$cmd = sprintf(
    "mysql -h %s -P %s -u %s %s %s < forta_db.sql",
    escapeshellarg($host),
    escapeshellarg($port),
    escapeshellarg($user),
    $pass !== "" && $pass !== null ? "-p" . escapeshellarg($pass) : "",
    escapeshellarg($db)
);
passthru($cmd, $ret);
if ($ret !== 0) {
    fwrite(STDERR, "Gagal import forta_db.sql via mysql CLI (code $ret)\n");
    exit($ret);
}
echo "Database forta_db.sql berhasil diimpor ke database: $db\n";
'

echo "[4/6] Menjalankan migrasi & sinkronisasi akun dosen serta file lampiran dummy..."
php artisan migrate --force
php artisan db:seed --class=LecturerAccountSeeder --force

php -r '
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SubmissionFile;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

foreach (SubmissionFile::all() as $file) {
    $relPath = $file->stored_path;
    if ($relPath && !Storage::disk("local")->exists($relPath)) {
        $dir = dirname($relPath);
        if (!Storage::disk("local")->exists($dir)) {
            Storage::disk("local")->makeDirectory($dir);
        }
        $fpdf = new Fpdi();
        $fpdf->AddPage();
        $fpdf->SetFont("Helvetica", "B", 14);
        $fpdf->Cell(0, 10, "UNIVERSITAS PRADITA - DOKUMEN LAMPIRAN", 0, 1, "C");
        $fpdf->SetFont("Helvetica", "", 11);
        $fpdf->Ln(5);
        $fpdf->Cell(0, 8, "Nama Berkas: " . ($file->original_name ?: basename($relPath)), 0, 1);
        $fpdf->Cell(0, 8, "Dokumen lampiran pengajuan mahasiswa pada sistem FORTA.", 0, 1);
        $fpdf->Output("F", Storage::disk("local")->path($relPath));
    }
}
echo "Berkas fisik PDF dummy telah diverifikasi di storage.\n";
'

echo "[5/6] Memperbarui symlink storage & kompilasi aset frontend (Vite)..."
php artisan storage:link || true
if command -v npm >/dev/null 2>&1; then
    npm install --no-audit --no-fund
    npm run build
fi

echo "[6/6] Membersihkan cache Laravel & merapikan permission..."
php artisan optimize:clear
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "===================================================="
echo "  UPDATE VPS SELESAI DENGAN SUKSES TANPA ERROR!"
echo "===================================================="
