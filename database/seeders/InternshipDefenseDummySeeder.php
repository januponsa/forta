<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\ActivityType;
use App\Models\Form;
use App\Models\FormField;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\DefenseCase;
use App\Models\DefenseSchedule;
use App\Models\DefenseAssignment;
use App\Models\RubricVersion;
use App\Models\RubricSection;
use App\Models\RubricItem;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\DefenseSuggestion;
use App\Models\LetterTemplate;
use App\Services\DefenseCalculationService;
use App\Services\DefenseDocumentGenerator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class InternshipDefenseDummySeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Seeding Rubric Versions...');
        $this->call(InternshipRubricSeeder::class);

        // 1. Ensure Letter Template
        $letterTemplate = LetterTemplate::firstOrCreate(
            ['type' => 'internship'],
            [
                'university_name' => 'UNIVERSITAS PRADITA',
                'campus_address' => 'Scientia Business Park Tower I, Jl. Scientia Boulevard Gading Serpong, Tangerang',
                'contact_info' => 'Telp: (021) 2967-3333 | info@pradita.ac.id',
                'city' => 'Tangerang',
                'letter_code' => 'KP/IF',
                'subject' => 'Permohonan Sidang Kerja Praktik',
                'opening_paragraph' => 'Dengan ini menerangkan bahwa mahasiswa berikut telah menyelesaikan kerja praktik:',
                'purpose_paragraph' => 'Guna memenuhi syarat kelulusan mata kuliah Kerja Praktik.',
                'closing_paragraph' => 'Demikian surat ini dibuat agar dapat dipergunakan sebagaimana mestinya.',
                'signatory_name' => 'Januponsa Dio Firizqi, S.Kom., M.Kom.',
                'signatory_position' => 'Koordinator Kerja Praktik',
                'number_format' => '{nomor_urut}/PRADITA/{kode_surat}/{bulan_romawi}/{tahun}',
            ]
        );

        // 2. Ensure Active Lecturers
        $lecturersData = [
            [
                'nip' => '031000010',
                'name' => 'Januponsa Dio Firizqi, S.Kom., M.Kom.',
                'email' => 'januponsa.dio@pradita.ac.id',
                'position' => 'Koordinator Kerja Praktik',
                'is_active' => true,
                'allowed_roles' => ['superadmin', 'admin_forta'],
            ],
            [
                'nip' => '031000011',
                'name' => 'Erick Dazki, S.Kom., M.Kom.',
                'email' => 'erick.dazki@pradita.ac.id',
                'position' => 'Dosen Pembimbing',
                'is_active' => true,
                'allowed_roles' => ['superadmin', 'admin_forta'],
            ],
            [
                'nip' => '0301018501',
                'name' => 'Dr. Ir. Budi Rahardjo, S.Kom., M.T.',
                'email' => 'budi.rahardjo@pradita.ac.id',
                'position' => 'Ketua Penguji Sidang',
                'is_active' => true,
                'allowed_roles' => ['superadmin', 'admin_forta'],
            ],
            [
                'nip' => '0302028602',
                'name' => 'Prof. Dr. Ir. Anita Yuliana, M.Kom.',
                'email' => 'anita.yuliana@pradita.ac.id',
                'position' => 'Dosen Penguji Senior',
                'is_active' => true,
                'allowed_roles' => ['superadmin', 'admin_forta'],
            ],
            [
                'nip' => '0303038703',
                'name' => 'Hendra Gunawan, S.Kom., M.Sc.',
                'email' => 'hendra.gunawan@pradita.ac.id',
                'position' => 'Dosen Pembimbing & Penguji',
                'is_active' => true,
                'allowed_roles' => ['superadmin', 'admin_forta'],
            ]
        ];

        $lecturers = [];
        foreach ($lecturersData as $ld) {
            $lecturers[] = Lecturer::updateOrCreate(['nip' => $ld['nip']], $ld);
        }

        // 3. Ensure Activity Type & Form Pendaftaran Sidang Magang
        $activityType = ActivityType::firstOrCreate(
            ['slug' => 'magang-kerja-praktik'],
            ['name' => 'Magang / Kerja Praktik', 'description' => 'Mata kuliah kerja praktik di industri.']
        );

        $adminUser = User::first() ?? User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@pradita.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin',
        ]);

        $form = Form::updateOrCreate(
            ['form_code' => 'INTERNSHIP_DEFENSE_REGISTRATION'],
            [
                'title' => 'Pendaftaran Sidang Kerja Praktik 2026',
                'slug' => 'pendaftaran-sidang-kerja-praktik-2026',
                'description' => 'Formulir resmi pendaftaran sidang kerja praktik/magang program studi Informatika.',
                'semester' => 'Genap 2025/2026',
                'phase' => 'registration',
                'activity_type_id' => $activityType->id,
                'open_at' => Carbon::now()->subMonths(1),
                'close_at' => Carbon::now()->addMonths(2),
                'status' => 'active',
            ]
        );

        // Fields required
        $fieldDefs = [
            ['name' => 'INTDEF_report_title', 'label' => 'Judul Laporan Kerja Praktik', 'type' => 'textarea', 'is_required' => true, 'order' => 1],
            ['name' => 'INTDEF_company_name', 'label' => 'Nama Perusahaan / Instansi', 'type' => 'text', 'is_required' => true, 'order' => 2],
            ['name' => 'INTDEF_company_address', 'label' => 'Alamat Perusahaan', 'type' => 'textarea', 'is_required' => false, 'order' => 3],
            ['name' => 'INTDEF_mentor_name', 'label' => 'Nama Mentor Lapangan', 'type' => 'text', 'is_required' => true, 'order' => 4],
            ['name' => 'INTDEF_mentor_position', 'label' => 'Jabatan Mentor Lapangan', 'type' => 'text', 'is_required' => false, 'order' => 5],
            ['name' => 'INTDEF_mentor_nip', 'label' => 'NIP / NIK Mentor', 'type' => 'text', 'is_required' => false, 'order' => 6],
            ['name' => 'INTDEF_internship_period', 'label' => 'Periode Magang', 'type' => 'text', 'is_required' => false, 'order' => 7],
            ['name' => 'INTDEF_mentor_evaluation_file', 'label' => 'Dokumen Penilaian Mentor Lapangan (PDF)', 'type' => 'file', 'is_required' => true, 'order' => 8, 'max_size_mb' => 5, 'allowed_types' => ['PDF']],
            ['name' => 'INTDEF_final_report_file', 'label' => 'Draft Laporan Kerja Praktik (PDF)', 'type' => 'file', 'is_required' => true, 'order' => 9, 'max_size_mb' => 10, 'allowed_types' => ['PDF']],
        ];

        $fields = [];
        foreach ($fieldDefs as $f) {
            $fields[$f['name']] = FormField::updateOrCreate(
                ['form_id' => $form->id, 'name' => $f['name']],
                $f
            );
        }

        // Helper to generate a minimal valid dummy PDF file for submissions
        $generateDummyPdf = function ($title, $relPath) {
            if (!Storage::disk('local')->exists($relPath)) {
                $dir = dirname($relPath);
                if (!Storage::disk('local')->exists($dir)) {
                    Storage::disk('local')->makeDirectory($dir);
                }
                
                $fpdf = new Fpdi();
                $fpdf->AddPage();
                $fpdf->SetFont('Helvetica', 'B', 14);
                $fpdf->Cell(0, 10, 'UNIVERSITAS PRADITA', 0, 1, 'C');
                $fpdf->SetFont('Helvetica', 'B', 12);
                $fpdf->Cell(0, 10, $title, 0, 1, 'C');
                $fpdf->SetFont('Helvetica', '', 10);
                $fpdf->Ln(5);
                $fpdf->Cell(0, 8, 'Dokumen ini merupakan lampiran resmi pengajuan sidang kerja praktik.', 0, 1);
                $fpdf->Cell(0, 8, 'Telah diverifikasi dan ditandatangani secara digital.', 0, 1);
                $fpdf->Ln(10);
                $fpdf->Cell(0, 8, 'Tanggal: ' . Carbon::now()->isoFormat('D MMMM Y'), 0, 1);

                $absPath = Storage::disk('local')->path($relPath);
                $fpdf->Output('F', $absPath);
            }
        };

        // 4. Fetch Rubrics
        $spvRubric = RubricVersion::where('role', 'supervisor')->where('is_active', true)->first();
        $exmRubric = RubricVersion::where('role', 'examiner')->where('is_active', true)->first();
        $menRubric = RubricVersion::where('role', 'mentor')->where('is_active', true)->first();

        // 5. Setup 8 Diverse Student Cases
        $candidates = [
            [
                'nim' => '2310101001',
                'name' => 'MUHAMMAD SULTHAN ARIF RIZKY PRASETYO',
                'stage' => 'registered', // Baru mendaftar
                'report_title' => 'Pengembangan Modul Otentikasi dan RBAC Menggunakan Laravel Sanctum pada PT Telekomunikasi Indonesia',
                'company' => 'PT Telekomunikasi Indonesia Tbk',
                'company_address' => 'Gedung Telkom Landmark Tower Lt. 38, Jl. Jend. Gatot Subroto, Jakarta Selatan',
                'mentor' => 'Ahmad Fauzi, S.T.',
                'mentor_position' => 'Lead Backend Engineer',
                'mentor_nip' => 'TLK-90812',
                'period' => '1 Februari 2026 - 30 April 2026',
            ],
            [
                'nim' => '2310101002',
                'name' => 'DASON TIOVINO',
                'stage' => 'waiting_schedule', // Dokumen diverifikasi, siap dijadwalkan
                'report_title' => 'Implementasi Pipeline CI/CD Berbasis GitHub Actions pada Core Banking PT Bank Central Asia Tbk',
                'company' => 'PT Bank Central Asia Tbk',
                'company_address' => 'Menara BCA Lt. 22, Grand Indonesia, Jl. M.H. Thamrin No. 1, Jakarta Pusat',
                'mentor' => 'Rendra Kusuma, M.Kom.',
                'mentor_position' => 'DevOps Specialist Lead',
                'mentor_nip' => 'BCA-88712',
                'period' => '15 Januari 2026 - 15 April 2026',
            ],
            [
                'nim' => '2310101003',
                'name' => 'MICKHAEL NIKLAS',
                'stage' => 'scheduled', // Sudah dijadwalkan sidang
                'report_title' => 'Analisis Sentimen Ulasan Pengguna Aplikasi E-Commerce Menggunakan IndoBERT pada PT Tokopedia',
                'company' => 'PT Tokopedia',
                'company_address' => 'Tokopedia Tower Ciputra World 2, Jl. Prof. DR. Satrio, Jakarta Selatan',
                'mentor' => 'Kevin Wijaya, S.Kom.',
                'mentor_position' => 'Senior Data Scientist',
                'mentor_nip' => 'TKP-77210',
                'period' => '5 Januari 2026 - 5 April 2026',
                'schedule_offset_days' => 3, // Sidang 3 hari lagi
                'start_time' => '09:00',
                'end_time' => '10:00',
                'room' => 'Lab Komputer 302 (Kampus Serpong)',
                'supervisor_idx' => 0,
                'examiner_idx' => 2,
            ],
            [
                'nim' => '2310101005',
                'name' => 'BRIAN ABRAHAM JORDAN SUNTPIET',
                'stage' => 'waiting_mentor_score', // Sidang selesai, menunggu admin input nilai mentor
                'report_title' => 'Perancangan UI/UX Dashboard Transaksi FinTech Menggunakan Design Thinking pada PT DANA Indonesia',
                'company' => 'PT Espay Debit Indonesia Koe (DANA)',
                'company_address' => 'Capital Place Office Tower Lt. 18, Jl. Jend. Gatot Subroto Kav. 18, Jakarta Selatan',
                'mentor' => 'Jessica Tan, S.Ds.',
                'mentor_position' => 'Product Design Lead',
                'mentor_nip' => 'DNA-54129',
                'period' => '10 Januari 2026 - 10 April 2026',
                'schedule_offset_days' => 0, // Sidang hari ini
                'start_time' => '10:30',
                'end_time' => '11:30',
                'room' => 'Ruang Sidang 401',
                'supervisor_idx' => 1,
                'examiner_idx' => 3,
            ],
            [
                'nim' => '2310101007',
                'name' => 'FARELL ALVARO THERIONO',
                'stage' => 'waiting_scores', // Nilai mentor sudah masuk, menunggu nilai dosen penguji/pembimbing
                'report_title' => 'Otomasi Pengujian End-to-End Menggunakan Playwright pada Platform PT Traveloka Indonesia',
                'company' => 'PT Traveloka Indonesia',
                'company_address' => 'Traveloka Campus BSD Green Office Park, BSD City, Tangerang',
                'mentor' => 'Dimas Aditya, S.T.',
                'mentor_position' => 'QA Automation Lead',
                'mentor_nip' => 'TVL-33910',
                'period' => '12 Januari 2026 - 12 April 2026',
                'schedule_offset_days' => -1, // Sidang kemarin
                'start_time' => '13:00',
                'end_time' => '14:00',
                'room' => 'Ruang Sidang 402',
                'supervisor_idx' => 0,
                'examiner_idx' => 4,
                'mentor_score' => 92.50,
            ],
            [
                'nim' => '2310101008',
                'name' => 'KENNETH WILLIAM GUNAWAN',
                'stage' => 'ready_to_finalize', // Semua nilai lengkap, siap rekap & generate berita acara
                'report_title' => 'Migrasi Arsitektur Monolith ke Microservices Berbasis Docker Kubernetes pada PT GoTo Tokopedia',
                'company' => 'PT GoTo Gojek Tokopedia Tbk',
                'company_address' => 'Pasaraya Blok M Gedung B Lt. 6, Jl. Iskandarsyah II No. 2, Jakarta Selatan',
                'mentor' => 'Bayu Wicaksono, M.T.',
                'mentor_position' => 'Principal Systems Architect',
                'mentor_nip' => 'GTO-10293',
                'period' => '1 Januari 2026 - 1 April 2026',
                'schedule_offset_days' => -2,
                'start_time' => '14:30',
                'end_time' => '15:30',
                'room' => 'Online via Zoom Meeting Room A',
                'supervisor_idx' => 1,
                'examiner_idx' => 2,
                'mentor_score' => 95.00,
                'supervisor_score' => 88.00,
                'examiner_score' => 90.00,
            ],
            [
                'nim' => '2310101010',
                'name' => 'ANDREW JATZEN LAY',
                'stage' => 'completed', // Lulus sempurna, dokumen berita acara sudah dicetak/digenerate
                'report_title' => 'Implementasi Sistem Deteksi Intrusi Jaringan Menggunakan Snort dan ELK Stack pada PT Astra International',
                'company' => 'PT Astra International Tbk',
                'company_address' => 'Menara Astra Lt. 15, Jl. Jend. Sudirman Kav. 5-6, Jakarta Pusat',
                'mentor' => 'Ir. Hendro Subroto',
                'mentor_position' => 'Head of IT Infrastructure',
                'mentor_nip' => 'AST-89012',
                'period' => '15 Januari 2026 - 15 April 2026',
                'schedule_offset_days' => -5,
                'start_time' => '09:00',
                'end_time' => '10:00',
                'room' => 'Ruang Sidang 401',
                'supervisor_idx' => 0,
                'examiner_idx' => 3,
                'mentor_score' => 96.67,
                'supervisor_score' => 95.00,
                'examiner_score' => 96.00,
            ],
            [
                'nim' => '2310101011',
                'name' => 'BRYAN ADIPUTRA ONGGO',
                'stage' => 'revision_required', // Lulus dengan catatan revisi perbaikan F6
                'report_title' => 'Rancang Bangun Sistem Antrean Digital Rumah Sakit Berbasis WebSocket pada Siloam Hospitals Group',
                'company' => 'PT Siloam International Hospitals Tbk',
                'company_address' => 'Gedung Fakultas Kedokteran UPH, Jl. Boulevard Jend. Sudirman, Lippo Karawaci, Tangerang',
                'mentor' => 'Drg. Maria Stephanie, M.M.S.I.',
                'mentor_position' => 'Head of Hospital Information System',
                'mentor_nip' => 'SLM-66231',
                'period' => '1 Februari 2026 - 30 April 2026',
                'schedule_offset_days' => -4,
                'start_time' => '11:00',
                'end_time' => '12:00',
                'room' => 'Ruang Sidang 402',
                'supervisor_idx' => 4,
                'examiner_idx' => 1,
                'mentor_score' => 84.00,
                'supervisor_score' => 80.00,
                'examiner_score' => 82.00,
                'suggestions' => [
                    [
                        'category' => 'Laporan',
                        'priority' => 'high',
                        'suggestion' => 'Perbaiki sitasi pustaka pada Bab 2 dan tambahkan diagram sequence alur antrean pada Bab 3.',
                        'status' => 'Belum Dikerjakan',
                    ],
                    [
                        'category' => 'Penyempurnaan Program',
                        'priority' => 'normal',
                        'suggestion' => 'Tambahkan mekanisme fallback polling jika koneksi WebSocket terputus.',
                        'status' => 'Sedang Dikerjakan',
                    ],
                ]
            ],
        ];

        $calcService = new DefenseCalculationService();
        $docGenerator = new DefenseDocumentGenerator();

        foreach ($candidates as $c) {
            $student = Student::firstOrCreate(
                ['nim' => $c['nim']],
                [
                    'name' => $c['name'],
                    'email' => Str::slug($c['name'], '.') . '@student.pradita.ac.id',
                    'angkatan' => 2023,
                    'program_studi' => 'Informatika',
                    'semester' => 'Genap 2025/2026',
                    'status_mahasiswa' => 'Aktif',
                    'is_active' => true,
                ]
            );

            // Files path
            $mentorFileRel = "submissions/mentor_eval_{$student->nim}.pdf";
            $reportFileRel = "submissions/report_{$student->nim}.pdf";
            $generateDummyPdf("LEMBAR PENILAIAN MENTOR - {$c['company']}", $mentorFileRel);
            $generateDummyPdf("LAPORAN KERJA PRAKTIK - {$c['report_title']}", $reportFileRel);

            // Create submission
            $submission = Submission::updateOrCreate(
                ['form_id' => $form->id, 'nim' => $student->nim],
                [
                    'name' => $student->name,
                    'email' => $student->email,
                    'status' => in_array($c['stage'], ['registered']) ? 'submitted' : 'approved',
                    'submitted_at' => Carbon::now()->subDays(10),
                    'answers' => [
                        'INTDEF_report_title' => $c['report_title'],
                        'INTDEF_company_name' => $c['company'],
                        'INTDEF_company_address' => $c['company_address'],
                        'INTDEF_mentor_name' => $c['mentor'],
                        'INTDEF_mentor_position' => $c['mentor_position'],
                        'INTDEF_mentor_nip' => $c['mentor_nip'],
                        'INTDEF_internship_period' => $c['period'],
                    ]
                ]
            );

            // Create submission files
            SubmissionFile::updateOrCreate(
                ['submission_id' => $submission->id, 'field_id' => $fields['INTDEF_mentor_evaluation_file']->id],
                [
                    'original_name' => "Form_Nilai_Mentor_{$student->nim}.pdf",
                    'stored_path' => $mentorFileRel,
                    'size_bytes' => 125000,
                    'mime_type' => 'application/pdf',
                    'uploaded_at' => Carbon::now()->subDays(10),
                ]
            );

            SubmissionFile::updateOrCreate(
                ['submission_id' => $submission->id, 'field_id' => $fields['INTDEF_final_report_file']->id],
                [
                    'original_name' => "Laporan_KP_{$student->nim}.pdf",
                    'stored_path' => $reportFileRel,
                    'size_bytes' => 840000,
                    'mime_type' => 'application/pdf',
                    'uploaded_at' => Carbon::now()->subDays(10),
                ]
            );

            // Metadata
            $metadata = [
                'company_name' => $c['company'],
                'company_address' => $c['company_address'],
                'mentor_name' => $c['mentor'],
                'mentor_position' => $c['mentor_position'],
                'mentor_nip' => $c['mentor_nip'],
                'report_title' => $c['report_title'],
                'internship_period' => $c['period'],
            ];

            // Create or update DefenseCase
            $defenseCase = DefenseCase::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'student_id' => $student->id,
                    'defense_type' => 'internship_defense',
                    'semester' => 'Genap 2025/2026',
                    'status' => $c['stage'],
                    'metadata' => $metadata,
                ]
            );

            // Schedules and Assignments
            if (isset($c['schedule_offset_days'])) {
                $schedDate = Carbon::now()->addDays($c['schedule_offset_days'])->toDateString();
                DefenseSchedule::updateOrCreate(
                    ['defense_case_id' => $defenseCase->id],
                    [
                        'date' => $schedDate,
                        'start_time' => $c['start_time'],
                        'end_time' => $c['end_time'],
                        'room_or_link' => $c['room'],
                        'notes' => 'Harap hadir 15 menit sebelum sidang dimulai.',
                    ]
                );

                $spvLecturer = $lecturers[$c['supervisor_idx']];
                $exmLecturer = $lecturers[$c['examiner_idx']];

                DefenseAssignment::updateOrCreate(
                    ['defense_case_id' => $defenseCase->id, 'role' => 'supervisor'],
                    ['lecturer_id' => $spvLecturer->id]
                );

                DefenseAssignment::updateOrCreate(
                    ['defense_case_id' => $defenseCase->id, 'role' => 'examiner'],
                    ['lecturer_id' => $exmLecturer->id]
                );
            }

            // Mentor Assessment
            if (isset($c['mentor_score']) && $menRubric) {
                $menAss = Assessment::updateOrCreate(
                    ['defense_case_id' => $defenseCase->id, 'assessor_role' => 'mentor'],
                    [
                        'rubric_version_id' => $menRubric->id,
                        'assessor_type' => 'admin',
                        'assessor_id' => $adminUser->id,
                        'total_score' => $c['mentor_score'],
                        'status' => 'final',
                        'finalized_at' => Carbon::now()->subDays(1),
                        'general_notes' => 'Mahasiswa menunjukkan kedisiplinan dan inisiatif tinggi selama magang.',
                    ]
                );

                // Populate scores for items
                foreach ($menRubric->sections as $sec) {
                    foreach ($sec->items as $item) {
                        AssessmentScore::updateOrCreate(
                            ['assessment_id' => $menAss->id, 'rubric_item_id' => $item->id],
                            ['score' => $c['mentor_score']]
                        );
                    }
                }
            }

            // Supervisor & Examiner Assessments
            if (isset($c['supervisor_score']) && $spvRubric) {
                $spvLecturer = $lecturers[$c['supervisor_idx']];
                $spvAss = Assessment::updateOrCreate(
                    ['defense_case_id' => $defenseCase->id, 'assessor_role' => 'supervisor'],
                    [
                        'rubric_version_id' => $spvRubric->id,
                        'assessor_type' => 'lecturer',
                        'assessor_id' => $spvLecturer->id,
                        'total_score' => $c['supervisor_score'],
                        'status' => 'final',
                        'finalized_at' => Carbon::now()->subDays(1),
                        'general_notes' => 'Laporan kerja praktik terstruktur baik.',
                    ]
                );

                // Populate scores
                foreach ($spvRubric->sections as $sec) {
                    foreach ($sec->items as $item) {
                        AssessmentScore::updateOrCreate(
                            ['assessment_id' => $spvAss->id, 'rubric_item_id' => $item->id],
                            ['score' => round($c['supervisor_score'] * ($item->max_score / 100), 2)]
                        );
                    }
                }
            }

            if (isset($c['examiner_score']) && $exmRubric) {
                $exmLecturer = $lecturers[$c['examiner_idx']];
                $exmAss = Assessment::updateOrCreate(
                    ['defense_case_id' => $defenseCase->id, 'assessor_role' => 'examiner'],
                    [
                        'rubric_version_id' => $exmRubric->id,
                        'assessor_type' => 'lecturer',
                        'assessor_id' => $exmLecturer->id,
                        'total_score' => $c['examiner_score'],
                        'status' => 'final',
                        'finalized_at' => Carbon::now()->subDays(1),
                        'originality_status' => 'Tidak Ada Indikasi Pelanggaran',
                        'originality_notes' => 'Hasil uji similarity Turnitin 12% (memenuhi standar).',
                        'general_notes' => 'Presentasi sangat jelas dan penguasaan materi sangat baik.',
                    ]
                );

                foreach ($exmRubric->sections as $sec) {
                    foreach ($sec->items as $item) {
                        AssessmentScore::updateOrCreate(
                            ['assessment_id' => $exmAss->id, 'rubric_item_id' => $item->id],
                            ['score' => round($c['examiner_score'] * ($item->max_score / 100), 2)]
                        );
                    }
                }
            }

            // Suggestions (F6)
            if (isset($c['suggestions'])) {
                $exmLecturer = $lecturers[$c['examiner_idx']];
                foreach ($c['suggestions'] as $sug) {
                    DefenseSuggestion::create([
                        'defense_case_id' => $defenseCase->id,
                        'lecturer_id' => $exmLecturer->id,
                        'lecturer_role' => 'examiner',
                        'category' => $sug['category'],
                        'priority' => $sug['priority'],
                        'suggestion' => $sug['suggestion'],
                        'status' => $sug['status'],
                    ]);
                }
            }

            // Recalculate score if stage warrants it
            if (in_array($c['stage'], ['ready_to_finalize', 'completed', 'passed', 'revision_required'])) {
                $calcService->calculateFinalDefenseScore($defenseCase);
                $defenseCase->refresh();

                // Override status if needed
                $defenseCase->update([
                    'status' => $c['stage'],
                    'finalized_at' => Carbon::now()->subDays(1),
                    'finalized_by' => $adminUser->id,
                ]);

                // For completed/passed, generate official documents
                if (in_array($c['stage'], ['completed', 'passed'])) {
                    try {
                        $docGenerator->generateAllDocuments($defenseCase);
                    } catch (\Throwable $th) {
                        // Document generation fallback
                    }
                }
            }
        }

        $this->command?->info('Internship defense dummy data seeded successfully for 8 students!');
    }
}
