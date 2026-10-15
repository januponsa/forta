<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use App\Models\Form;
use App\Models\Student;
use App\Models\StudentRegistrationRequest;
use App\Models\Submission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AiDashboardDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding data dummy untuk AI Analytics Dashboard...');

        // 1. Pastikan Jenis Kegiatan tersedia
        $taType = ActivityType::firstOrCreate(
            ['slug' => 'tugas-akhir'],
            ['name' => 'Tugas Akhir']
        );

        $magangType = ActivityType::firstOrCreate(
            ['slug' => 'magang-kerja-praktik'],
            ['name' => 'Magang / Kerja Praktik']
        );

        // 2. Pastikan Forms tersedia (4 Form Aktif untuk Chart)
        $form1 = Form::firstOrCreate(
            ['slug' => 'pendaftaran-ta-ganjil-2025-2026'],
            [
                'title' => 'Pendaftaran TA Ganjil 2025/2026',
                'description' => 'Pendaftaran awal Tugas Akhir semester ganjil.',
                'activity_type_id' => $taType->id,
                'phase' => 'registration',
                'semester' => 'Ganjil 2025/2026',
                'open_at' => now()->subMonths(1),
                'close_at' => now()->addMonths(2),
                'status' => 'active',
            ]
        );

        $form2 = Form::firstOrCreate(
            ['slug' => 'magang-2026'],
            [
                'title' => 'Pengajuan Magang 2026',
                'description' => 'Pengajuan surat permohonan dan tempat magang.',
                'activity_type_id' => $magangType->id,
                'phase' => 'registration',
                'semester' => 'Genap 2025/2026',
                'open_at' => now()->subWeeks(3),
                'close_at' => now()->addMonths(1),
                'status' => 'active',
            ]
        );

        $form3 = Form::firstOrCreate(
            ['slug' => 'seminar-proposal-skripsi-2026'],
            [
                'title' => 'Seminar Proposal Skripsi 2026',
                'description' => 'Pendaftaran jadwal dan berkas seminar proposal.',
                'activity_type_id' => $taType->id,
                'phase' => 'registration',
                'semester' => 'Genap 2025/2026',
                'open_at' => now()->subWeeks(2),
                'close_at' => now()->addMonths(1),
                'status' => 'active',
            ]
        );

        $form4 = Form::firstOrCreate(
            ['slug' => 'sidang-akhir-yudisium-2026'],
            [
                'title' => 'Sidang Akhir & Yudisium 2026',
                'description' => 'Pengajuan sidang meja hijau dan verifikasi kelulusan.',
                'activity_type_id' => $taType->id,
                'phase' => 'reporting',
                'semester' => 'Genap 2025/2026',
                'open_at' => now()->subDays(10),
                'close_at' => now()->addMonths(1),
                'status' => 'active',
            ]
        );

        // Ambil daftar mahasiswa yang ada
        $students = Student::orderBy('id')->get();
        if ($students->count() < 10) {
            $this->command->error('Jumlah data mahasiswa kurang dari 10. Jalankan roster sync terlebih dahulu.');
            return;
        }

        // Hapus dummy submissions lama untuk mahasiswa uji coba at-risk (agar seeder idempotent)
        $testStudentNims = $students->slice(0, 5)->pluck('nim')->toArray();
        Submission::whereIn('nim', $testStudentNims)->delete();

        // 3. Setup Mahasiswa Berisiko (At-Risk Students)
        // Mahasiswa 1: Risiko TINGGI (Score: 80) -> 2 rejected (+60) + 1 draft (+20)
        $mhs1 = $students[0];
        Submission::create([
            'form_id' => $form1->id,
            'nim' => $mhs1->nim,
            'name' => $mhs1->name,
            'email' => $mhs1->email,
            'status' => 'draft',
            'answers' => ['judul' => 'Sistem Rekomendasi Karir Menggunakan AI', 'catatan' => 'Draft belum selesai'],
            'created_at' => now()->subDays(12),
            'updated_at' => now()->subDays(5),
        ]);
        Submission::create([
            'form_id' => $form2->id,
            'nim' => $mhs1->nim,
            'name' => $mhs1->name,
            'email' => $mhs1->email,
            'status' => 'rejected',
            'answers' => ['tempat_magang' => 'PT Gojek Indonesia', 'posisi' => 'Software Engineer Intern'],
            'review_note' => 'Surat penerimaan magang belum ada stempel basah perusahaan.',
            'reviewed_by' => 1,
            'reviewed_at' => now()->subDays(6),
            'submitted_at' => now()->subDays(8),
            'created_at' => now()->subDays(9),
        ]);
        Submission::create([
            'form_id' => $form3->id,
            'nim' => $mhs1->nim,
            'name' => $mhs1->name,
            'email' => $mhs1->email,
            'status' => 'rejected',
            'answers' => ['judul_proposal' => 'Pengembangan Sistem Rekomendasi Karir AI'],
            'review_note' => 'Bab 1 dan Bab 2 belum disetujui oleh Dosen Pembimbing Utama.',
            'reviewed_by' => 1,
            'reviewed_at' => now()->subDays(2),
            'submitted_at' => now()->subDays(4),
            'created_at' => now()->subDays(5),
        ]);

        // Mahasiswa 2: Risiko TINGGI (Score: 70) -> 2 draft (+40) + 1 rejected (+30)
        $mhs2 = $students[1];
        Submission::create([
            'form_id' => $form1->id,
            'nim' => $mhs2->nim,
            'name' => $mhs2->name,
            'email' => $mhs2->email,
            'status' => 'draft',
            'answers' => ['judul' => 'Analisis Sentimen Twitter dengan BERT'],
            'created_at' => now()->subDays(15),
        ]);
        Submission::create([
            'form_id' => $form2->id,
            'nim' => $mhs2->nim,
            'name' => $mhs2->name,
            'email' => $mhs2->email,
            'status' => 'draft',
            'answers' => ['instansi' => 'PT Teknologi Digital Nusantara'],
            'created_at' => now()->subDays(10),
        ]);
        Submission::create([
            'form_id' => $form3->id,
            'nim' => $mhs2->nim,
            'name' => $mhs2->name,
            'email' => $mhs2->email,
            'status' => 'rejected',
            'answers' => ['judul_proposal' => 'Implementasi NLP untuk Analisis Sentimen'],
            'review_note' => 'Transkrip nilai tidak memenuhi syarat minimum SKS lulus.',
            'reviewed_by' => 1,
            'reviewed_at' => now()->subDays(3),
            'submitted_at' => now()->subDays(5),
            'created_at' => now()->subDays(6),
        ]);

        // Mahasiswa 3: Risiko SEDANG (Score: 60) -> 3 drafts (+60)
        $mhs3 = $students[2];
        foreach ([$form1, $form2, $form3] as $f) {
            Submission::create([
                'form_id' => $f->id,
                'nim' => $mhs3->nim,
                'name' => $mhs3->name,
                'email' => $mhs3->email,
                'status' => 'draft',
                'answers' => ['catatan' => 'Form masih dalam proses pengisian mahasiswa'],
                'created_at' => now()->subDays(8),
            ]);
        }

        // Mahasiswa 4: Risiko SEDANG (Score: 60) -> 2 rejected (+60)
        $mhs4 = $students[3];
        Submission::create([
            'form_id' => $form1->id,
            'nim' => $mhs4->nim,
            'name' => $mhs4->name,
            'email' => $mhs4->email,
            'status' => 'rejected',
            'answers' => ['judul' => 'Aplikasi Monitoring Kehadiran IoT'],
            'review_note' => 'Topik penelitian overlap dengan judul angkatan sebelumnya.',
            'reviewed_by' => 1,
            'reviewed_at' => now()->subDays(7),
            'submitted_at' => now()->subDays(9),
            'created_at' => now()->subDays(10),
        ]);
        Submission::create([
            'form_id' => $form4->id,
            'nim' => $mhs4->nim,
            'name' => $mhs4->name,
            'email' => $mhs4->email,
            'status' => 'rejected',
            'answers' => ['judul_skripsi' => 'Aplikasi Monitoring Kehadiran IoT'],
            'review_note' => 'Bukti sertifikasi keahlian belum diunggah.',
            'reviewed_by' => 1,
            'reviewed_at' => now()->subDays(1),
            'submitted_at' => now()->subDays(3),
            'created_at' => now()->subDays(4),
        ]);

        // Mahasiswa 5: Risiko SEDANG (Score: 50) -> Terdaftar > 7 hari tapi belum pernah submit pengajuan apapun
        $mhs5 = $students[4];
        // Pastikan tidak ada submission untuk mhs5
        Submission::where('nim', $mhs5->nim)->delete();
        StudentRegistrationRequest::updateOrCreate(
            ['student_id' => $mhs5->id],
            [
                'google_id' => 'dummy_google_id_' . $mhs5->nim,
                'google_email' => $mhs5->email,
                'normalized_email' => $mhs5->email,
                'nim' => $mhs5->nim,
                'name' => $mhs5->name,
                'angkatan' => $mhs5->angkatan ?? '2023',
                'status' => 'approved',
                'requested_at' => now()->subDays(14),
                'reviewed_at' => now()->subDays(13),
                'reviewed_by' => 1,
                'created_at' => now()->subDays(14),
                'updated_at' => now()->subDays(13),
            ]
        );

        // 4. Populasi data statistik submission umum (agar chart visual berbentuk kaya dan menarik)
        $poolStudents = $students->slice(5, 45); // 40 mahasiswa berikutnya
        $formList = [$form1, $form2, $form3, $form4];

        // Format sebaran submission per form:
        // Form 1: 22 submitted, 6 draft
        // Form 2: 17 submitted, 5 draft
        // Form 3: 13 submitted, 4 draft
        // Form 4: 9 submitted, 2 draft
        $configs = [
            $form1->id => ['submitted' => 20, 'draft' => 5],
            $form2->id => ['submitted' => 15, 'draft' => 4],
            $form3->id => ['submitted' => 11, 'draft' => 3],
            $form4->id => ['submitted' => 8, 'draft' => 2],
        ];

        foreach ($configs as $formId => $counts) {
            $studentIndex = 0;
            // Buat Submitted/Approved
            for ($i = 0; $i < $counts['submitted']; $i++) {
                if (isset($poolStudents[$studentIndex + 5])) {
                    $st = $poolStudents[$studentIndex + 5];
                    Submission::updateOrCreate(
                        ['form_id' => $formId, 'nim' => $st->nim],
                        [
                            'name' => $st->name,
                            'email' => $st->email,
                            'status' => ($i % 3 === 0) ? 'approved' : 'submitted',
                            'answers' => [
                                'judul' => 'Riset Akademik Mandiri Bidang ' . ($i % 2 === 0 ? 'Sistem Informasi' : 'Informatika'),
                                'link' => 'https://drive.google.com/sample-' . $st->nim
                            ],
                            'submitted_at' => now()->subDays(rand(1, 20)),
                            'created_at' => now()->subDays(rand(2, 22)),
                        ]
                    );
                    $studentIndex++;
                }
            }

            // Buat Draft
            for ($j = 0; $j < $counts['draft']; $j++) {
                if (isset($poolStudents[$studentIndex + 5])) {
                    $st = $poolStudents[$studentIndex + 5];
                    Submission::updateOrCreate(
                        ['form_id' => $formId, 'nim' => $st->nim],
                        [
                            'name' => $st->name,
                            'email' => $st->email,
                            'status' => 'draft',
                            'answers' => ['catatan' => 'Draft pengisian awal'],
                            'created_at' => now()->subDays(rand(1, 10)),
                        ]
                    );
                    $studentIndex++;
                }
            }
        }

        $this->command->info('Data dummy untuk AI Analytics Dashboard berhasil dibuat!');
    }
}
