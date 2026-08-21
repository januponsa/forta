import sys

file_path = r'c:\Users\userJ\Documents\fortain\database\seeders\InternshipRubricSeeder.php'

seeder_code = """<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternshipRubricSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Pembimbing (Supervisor) - 30%
        $spvId = DB::table('rubric_versions')->insertGetId([
            'defense_type' => 'internship_defense',
            'role' => 'supervisor',
            'version_name' => 'v1.0',
            'is_active' => true,
            'weight_percentage' => 30.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $spvSecA = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $spvId, 'name' => 'A. Ketekunan Mahasiswa', 'max_score' => 20, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $spvSecA, 'code' => 'A1', 'description' => 'Referensi/Studi Pustaka: Keterbaruan bahan pustaka yang digunakan dalam penulisan laporan serta banyaknya referensi terkait.', 'max_score' => 10, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $spvSecA, 'code' => 'A2', 'description' => 'Keaktifan mahasiswa berkonsultasi dengan dosen pembimbing untuk mendiskusikan laporan.', 'max_score' => 10, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $spvSecB = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $spvId, 'name' => 'B. Isi Laporan', 'max_score' => 80, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $spvSecB, 'code' => 'B1', 'description' => 'Kemampuan menulis laporan: Kepatuhan penulisan ilmiah sesuai buku petunjuk kerja praktik, termasuk penggunaan Bahasa Indonesia yang baik dan benar.', 'max_score' => 20, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $spvSecB, 'code' => 'B2', 'description' => 'Hasil Pengamatan: Ketelitian memilih observasi lapangan yang diangkat menjadi topik.', 'max_score' => 30, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $spvSecB, 'code' => 'B3', 'description' => 'Topik/Pembahasan: Ketajaman menganalisis permasalahan pada topik.', 'max_score' => 30, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Penguji (Examiner) - 30%
        $exmId = DB::table('rubric_versions')->insertGetId([
            'defense_type' => 'internship_defense',
            'role' => 'examiner',
            'version_name' => 'v1.0',
            'is_active' => true,
            'weight_percentage' => 30.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $exmSecA = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $exmId, 'name' => 'A. Pemahaman Isi Laporan', 'max_score' => 40, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $exmSecA, 'code' => 'A1', 'description' => 'Materi Laporan: Penguasaan mahasiswa terhadap materi laporan kerja praktik.', 'max_score' => 15, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $exmSecA, 'code' => 'A2', 'description' => 'Materi Presentasi: Penguasaan mahasiswa terhadap materi presentasi.', 'max_score' => 15, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $exmSecA, 'code' => 'A3', 'description' => 'Penyajian: Strukturisasi penyajian presentasi yang disampaikan.', 'max_score' => 10, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $exmSecB = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $exmId, 'name' => 'B. Laporan Kerja Praktik', 'max_score' => 30, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $exmSecB, 'code' => 'B1', 'description' => 'Topik/Pembahasan: Ketepatan memilih temuan observasi dan menganalisis temuan observasi.', 'max_score' => 15, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $exmSecB, 'code' => 'B2', 'description' => 'Manfaat pembahasan: Pembahasan memberikan manfaat dan pembekalan pekerjaan lapangan bagi peserta kerja praktik.', 'max_score' => 15, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $exmSecC = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $exmId, 'name' => 'C. Sidang/Seminar Kerja Praktik', 'max_score' => 30, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $exmSecC, 'code' => 'C1', 'description' => 'Sikap: Kepatuhan mahasiswa terhadap tata tertib sidang.', 'max_score' => 10, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $exmSecC, 'code' => 'C2', 'description' => 'Presentasi: Efektivitas presentasi selama persidangan.', 'max_score' => 10, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $exmSecC, 'code' => 'C3', 'description' => 'Kemampuan penyajian dan menjawab pertanyaan: Kejelasan serta sistematika presentasi dan jawaban.', 'max_score' => 10, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Mentor (Admin Inputs) - 40%
        $menId = DB::table('rubric_versions')->insertGetId([
            'defense_type' => 'internship_defense',
            'role' => 'mentor',
            'version_name' => 'v1.0',
            'is_active' => true,
            'weight_percentage' => 40.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Each section in mentor is conceptually 100 max for each item, but they are averaged. We will set max_score to 100 for all items.
        // Formula: rata-rata A1, B1, B2, C1, C2, D1, D2, E1, E2
        $menSecA = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $menId, 'name' => 'A. Berpikir kritis, kreatif/inisiatif, dan analitis', 'max_score' => 100, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $menSecA, 'code' => 'A1', 'description' => 'Mahasiswa mampu mengidentifikasi isu utama dalam masalah di lapangan dan berinisiatif selama kerja praktik.', 'max_score' => 100, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $menSecB = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $menId, 'name' => 'B. Komunikasi', 'max_score' => 200, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $menSecB, 'code' => 'B1', 'description' => 'Kemampuan berkomunikasi secara lisan.', 'max_score' => 100, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $menSecB, 'code' => 'B2', 'description' => 'Kemampuan berkomunikasi secara tertulis.', 'max_score' => 100, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $menSecC = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $menId, 'name' => 'C. Etika', 'max_score' => 200, 'display_order' => 3, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $menSecC, 'code' => 'C1', 'description' => 'Sopan santun selama kerja praktik.', 'max_score' => 100, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $menSecC, 'code' => 'C2', 'description' => 'Menghormati sesama selama kerja praktik.', 'max_score' => 100, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $menSecD = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $menId, 'name' => 'D. Kedisiplinan', 'max_score' => 200, 'display_order' => 4, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $menSecD, 'code' => 'D1', 'description' => 'Kedisiplinan waktu dalam bekerja.', 'max_score' => 100, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $menSecD, 'code' => 'D2', 'description' => 'Ketaatan terhadap peraturan perusahaan.', 'max_score' => 100, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $menSecE = DB::table('rubric_sections')->insertGetId(['rubric_version_id' => $menId, 'name' => 'E. Kerja mandiri dan tim', 'max_score' => 200, 'display_order' => 5, 'created_at' => $now, 'updated_at' => $now]);
        DB::table('rubric_items')->insert([
            ['rubric_section_id' => $menSecE, 'code' => 'E1', 'description' => 'Kemampuan bekerja secara mandiri.', 'max_score' => 100, 'display_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rubric_section_id' => $menSecE, 'code' => 'E2', 'description' => 'Kemampuan bekerja dalam tim.', 'max_score' => 100, 'display_order' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(seeder_code)
