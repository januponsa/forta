#!/usr/bin/env bash
set -e

echo "===================================================="
echo "  FORTA VPS AUTO-UPDATE & DATABASE SYNC SCRIPT"
echo "===================================================="

echo "[1/7] Menarik kode terbaru dari GitHub (origin/main)..."
git fetch origin main
git reset --hard origin/main

echo "[2/7] Memastikan konfigurasi Google OAuth di file .env VPS..."
php -r '
$envFile = ".env";
if (file_exists($envFile)) {
    $env = file_get_contents($envFile);
    $defaults = [
        "GOOGLE_STUDENT_REDIRECT_URI" => "https://forta.patriontech.com/auth/google/student/callback",
        "GOOGLE_ADMIN_REDIRECT_URI" => "https://forta.patriontech.com/auth/google/admin/callback",
        "STUDENT_DOMAIN" => "student.pradita.ac.id",
        "ADMIN_DOMAIN" => "pradita.ac.id",
    ];
    foreach ($defaults as $key => $val) {
        if (preg_match("/^{$key}=(.*)$/m", $env, $m)) {
            $current = trim($m[1], " \t\n\r\0\x0B\"'\''");
            if ($current === "" || str_contains($current, "localhost")) {
                $env = preg_replace("/^{$key}=.*$/m", "{$key}={$val}", $env);
            }
        } else {
            $env = rtrim($env) . "\n{$key}={$val}\n";
        }
    }
    file_put_contents($envFile, $env);
    echo "Konfigurasi Google OAuth pada .env VPS telah diverifikasi.\n";
}
'

echo "[3/7] Memastikan dependensi Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "[4/7] Mengimpor database terbaru (forta_db.sql) termasuk seluruh data dummy & akun dosen..."
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

echo "[5/7] Menjalankan migrasi & sinkronisasi akun dosen serta file lampiran dummy..."
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

echo "[6/7] Memperbarui symlink storage & kompilasi aset frontend (Vite)..."
php artisan storage:link || true
if command -v npm >/dev/null 2>&1; then
    npm install --no-audit --no-fund
    npm run build
fi

echo "[7/7] Membersihkan cache Laravel & merapikan permission..."
php artisan optimize:clear
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "===================================================="
echo "  UPDATE VPS SELESAI DENGAN SUKSES TANPA ERROR!"
echo "===================================================="
