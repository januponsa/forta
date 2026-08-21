<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use App\Models\Form;
use App\Models\FormField;
use App\Models\ReminderRecipient;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@pradita.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin',
        ]);

        // 2. Jenis Kegiatan
        $kegiatan = [
            'Tugas Akhir',
            'Konversi Mata Kuliah',
            'KKN',
            'Magang / Kerja Praktik',
        ];

        foreach ($kegiatan as $k) {
            ActivityType::create([
                'name' => $k,
                'slug' => Str::slug($k),
            ]);
        }

        // 3. Form Aktif
        $taType = ActivityType::where('name', 'Tugas Akhir')->first();

        $form = Form::create([
            'title' => 'Pendaftaran TA Ganjil 2025/2026',
            'description' => 'Form pendaftaran awal Tugas Akhir untuk mahasiswa semester ganjil TA 2025/2026.',
            'slug' => 'pendaftaran-ta-ganjil-2025-2026',
            'activity_type_id' => $taType->id,
            'phase' => 'registration',
            'semester' => 'Ganjil 2025/2026',
            'open_at' => now(),
            'close_at' => now()->addMonths(1),
            'status' => 'active',
        ]);

        // Form Fields
        FormField::create([
            'form_id' => $form->id,
            'label' => 'Judul TA',
            'type' => 'textarea',
            'is_required' => true,
            'order' => 1,
        ]);

        FormField::create([
            'form_id' => $form->id,
            'label' => 'Nama Dosen Pembimbing',
            'type' => 'text',
            'is_required' => true,
            'order' => 2,
        ]);

        FormField::create([
            'form_id' => $form->id,
            'label' => 'Bidang Penelitian',
            'type' => 'select',
            'options' => [
                'Kecerdasan Buatan',
                'Rekayasa Perangkat Lunak',
                'Jaringan & Keamanan',
                'Sistem Informasi',
                'Data Science',
                'Lainnya',
            ],
            'is_required' => true,
            'order' => 3,
        ]);

        FormField::create([
            'form_id' => $form->id,
            'label' => 'Link Proposal Drive',
            'type' => 'url',
            'is_required' => true,
            'order' => 4,
        ]);

        FormField::create([
            'form_id' => $form->id,
            'label' => 'Upload Lembar Persetujuan',
            'type' => 'file',
            'is_required' => true,
            'order' => 5,
            'max_files' => 1,
            'max_size_mb' => 2,
            'allowed_types' => ['PDF'],
        ]);

        // 4. Mahasiswa
        \Illuminate\Support\Facades\Artisan::call('forta:students:sync-official-roster', ['--apply' => true]);

        // 5. Penerima Reminder
        ReminderRecipient::create(['name' => 'Kaprodi', 'email' => 'kaprodi@pradita.ac.id', 'role' => 'kaprodi']);
        ReminderRecipient::create(['name' => 'Sekprodi', 'email' => 'sekprodi@pradita.ac.id', 'role' => 'sekprodi']);
    }
}
