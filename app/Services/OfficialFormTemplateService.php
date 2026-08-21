<?php

namespace App\Services;

class OfficialFormTemplateService
{
    public function getTemplates()
    {
        return [
            $this->getKknRegistration(),
            $this->getInternshipRegistration(),
            $this->getThesisRegistration(),
            $this->getThesisDefenseRegistration(),
            $this->getCourseConversionRegistration(),
            $this->getCourseConversionResult(),
            $this->getInternshipDefenseRegistration(),
        ];
    }

    protected function getKknRegistration()
    {
        return [
            'form_code' => 'KKN_REGISTRATION',
            'nama' => 'Pendaftaran KKN',
            'jenis_kegiatan' => 'KKN',
            'activity_type_slug' => 'kkn',
            'fase' => 'registration',
            'description' => 'Pendaftaran KKN Program Studi Informatika Universitas Pradita',
            'semester' => 'Ganjil',
            'depends_on_form_code' => null,
            'sections' => [
                [
                    'section_code' => 'KKN_SEC_IDENTITY',
                    'title' => 'Identitas Mahasiswa',
                    'order' => 1,
                    'fields' => [
                        ['name' => 'KKN_nim', 'label' => 'NIM', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'KKN_student_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'KKN_cohort', 'label' => 'Angkatan', 'type' => 'text', 'is_required' => true, 'order' => 3],
                        ['name' => 'KKN_student_phone', 'label' => 'Nomor WhatsApp Aktif', 'type' => 'text', 'is_required' => true, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'KKN_SEC_PLAN',
                    'title' => 'Rencana Kegiatan KKN',
                    'order' => 2,
                    'fields' => [
                        [
                            'name' => 'KKN_kkn_type', 
                            'label' => 'Bentuk KKN yang diajukan', 
                            'type' => 'radio', 
                            'is_required' => true, 
                            'options' => ['Pengembangan Aplikasi, Sistem, atau Website', 'Penyuluhan, Pelatihan, atau Pendampingan Teknologi', 'IoT atau Produk Teknologi Fisik', 'Proyek Teknologi Pengabdian atau Penelitian Dosen', 'Asisten Laboratorium atau Asisten Dosen'],
                            'order' => 1
                        ],
                        ['name' => 'KKN_provisional_title', 'label' => 'Judul sementara KKN', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'KKN_activity_description', 'label' => 'Deskripsi singkat rencana kegiatan', 'type' => 'textarea', 'is_required' => true, 'order' => 3],
                    ]
                ],
                [
                    'section_code' => 'KKN_SEC_PARTNER',
                    'title' => 'Mitra atau Lokasi KKN',
                    'order' => 3,
                    'fields' => [
                        ['name' => 'KKN_partner_name', 'label' => 'Nama Mitra atau Instansi Tujuan KKN', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'KKN_location_address', 'label' => 'Alamat/lokasi kegiatan', 'type' => 'textarea', 'is_required' => true, 'order' => 2],
                        ['name' => 'KKN_partner_type', 'label' => 'Jenis Mitra', 'type' => 'select', 'is_required' => true, 'options' => ['Desa/Kelurahan', 'Sekolah', 'UMKM', 'Perusahaan', 'Instansi Pemerintah', 'Laboratorium', 'Unit Internal Universitas', 'Lainnya'], 'order' => 3],
                        ['name' => 'KKN_partner_approval_status', 'label' => 'Apakah tempat/kegiatan sudah menyetujui rencana KKN?', 'type' => 'radio', 'is_required' => true, 'options' => ['Sudah', 'Belum', 'Masih proses koordinasi'], 'order' => 4],
                        
                        // Conditional: Sudah
                        ['name' => 'KKN_contact_person_name', 'label' => 'Nama penanggung jawab tempat/kegiatan', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_partner_approval_status', 'operator' => 'equals', 'value' => 'Sudah'], 'order' => 5],
                        ['name' => 'KKN_contact_person_role', 'label' => 'Jabatan penanggung jawab', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_partner_approval_status', 'operator' => 'equals', 'value' => 'Sudah'], 'order' => 6],
                        ['name' => 'KKN_contact_person_phone', 'label' => 'Nomor kontak penanggung jawab', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_partner_approval_status', 'operator' => 'equals', 'value' => 'Sudah'], 'order' => 7],
                        ['name' => 'KKN_partner_approval_file', 'label' => 'Bukti persetujuan mitra', 'type' => 'file', 'is_required' => true, 'allowed_types' => ['pdf', 'jpg', 'png'], 'max_size_mb' => 2, 'conditions' => ['trigger_field_id' => 'KKN_partner_approval_status', 'operator' => 'equals', 'value' => 'Sudah'], 'order' => 8],
                        
                        // Conditional: Belum
                        ['name' => 'KKN_unapproved_reason', 'label' => 'Alasan belum memperoleh persetujuan', 'type' => 'textarea', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_partner_approval_status', 'operator' => 'equals', 'value' => 'Belum'], 'order' => 9],
                        ['name' => 'KKN_followup_plan', 'label' => 'Rencana tindak lanjut', 'type' => 'textarea', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_partner_approval_status', 'operator' => 'equals', 'value' => 'Belum'], 'order' => 10],

                        // Conditional: Masih proses koordinasi
                        ['name' => 'KKN_coordination_status', 'label' => 'Status koordinasi terakhir', 'type' => 'textarea', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_partner_approval_status', 'operator' => 'equals', 'value' => 'Masih proses koordinasi'], 'order' => 11],
                    ]
                ],
                [
                    'section_code' => 'KKN_SEC_SCHEDULE_OUTPUT',
                    'title' => 'Jadwal dan Luaran',
                    'order' => 4,
                    'fields' => [
                        ['name' => 'KKN_planned_start_date', 'label' => 'Perkiraan tanggal mulai KKN', 'type' => 'date', 'is_required' => true, 'order' => 1],
                        ['name' => 'KKN_planned_end_date', 'label' => 'Perkiraan tanggal selesai KKN', 'type' => 'date', 'is_required' => true, 'order' => 2],
                        ['name' => 'KKN_estimated_work_hours', 'label' => 'Estimasi total jam kerja efektif', 'type' => 'number', 'is_required' => true, 'order' => 3],
                        ['name' => 'KKN_planned_outputs', 'label' => 'Rencana luaran KKN', 'type' => 'checkbox', 'is_required' => true, 'options' => ['Aplikasi', 'Website', 'Sistem informasi', 'Dashboard', 'Modul/materi pelatihan', 'Video tutorial', 'Produk IoT/prototype', 'Dokumentasi teknis', 'Manual penggunaan', 'Lainnya'], 'order' => 4],
                        ['name' => 'KKN_other_output', 'label' => 'Sebutkan rencana luaran lainnya', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_planned_outputs', 'operator' => 'equals', 'value' => 'Lainnya'], 'order' => 5],
                    ]
                ],
                [
                    'section_code' => 'KKN_SEC_BUDGET',
                    'title' => 'Anggaran dan Dokumen',
                    'order' => 5,
                    'fields' => [
                        ['name' => 'KKN_requires_special_budget', 'label' => 'Apakah kegiatan menggunakan anggaran khusus?', 'type' => 'radio', 'is_required' => true, 'options' => ['Ya', 'Tidak', 'Belum diketahui'], 'order' => 1],
                        
                        // Conditional: Ya
                        ['name' => 'KKN_estimated_budget', 'label' => 'Estimasi anggaran', 'type' => 'number', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_requires_special_budget', 'operator' => 'equals', 'value' => 'Ya'], 'order' => 2],
                        ['name' => 'KKN_funding_source', 'label' => 'Sumber dana', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_requires_special_budget', 'operator' => 'equals', 'value' => 'Ya'], 'order' => 3],
                        ['name' => 'KKN_budget_details', 'label' => 'Rincian penggunaan dana', 'type' => 'textarea', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'KKN_requires_special_budget', 'operator' => 'equals', 'value' => 'Ya'], 'order' => 4],
                        ['name' => 'KKN_budget_file', 'label' => 'Upload RAB', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'conditions' => ['trigger_field_id' => 'KKN_requires_special_budget', 'operator' => 'equals', 'value' => 'Ya'], 'order' => 5],
                    ]
                ],
                [
                    'section_code' => 'KKN_SEC_DECLARATION',
                    'title' => 'Pernyataan dan Pengiriman',
                    'order' => 6,
                    'fields' => [
                        ['name' => 'KKN_declarations', 'label' => 'Pernyataan mahasiswa', 'type' => 'checkbox', 'is_required' => true, 'options' => ['Saya menyatakan data yang diisi benar.', 'Saya bersedia mengikuti ketentuan KKN Prodi Informatika.', 'Saya bersedia membuat proposal, logbook individu, laporan akhir, dan bukti pendukung.', 'Saya memahami kegiatan KKN wajib memenuhi minimal 90 jam kerja efektif.', 'Saya memahami pengajuan ini akan divalidasi oleh Program Studi/DPL.'], 'order' => 1],
                    ]
                ],
            ]
        ];
    }
    protected function getInternshipRegistration()
    {
        return [
            'form_code' => 'INTERNSHIP_REGISTRATION',
            'nama' => 'Pendaftaran Magang/Kerja Praktik',
            'jenis_kegiatan' => 'Magang / Kerja Praktik',
            'activity_type_slug' => 'magang-kp',
            'fase' => 'registration',
            'description' => 'Pendaftaran Magang atau Kerja Praktik Mahasiswa',
            'semester' => 'Ganjil',
            'depends_on_form_code' => null,
            'sections' => [
                [
                    'section_code' => 'INT_SEC_IDENTITY',
                    'title' => 'Identitas Mahasiswa',
                    'order' => 1,
                    'fields' => [
                        ['name' => 'INT_nim', 'label' => 'NIM', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'INT_student_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'INT_cohort', 'label' => 'Angkatan', 'type' => 'text', 'is_required' => true, 'order' => 3],
                        ['name' => 'INT_student_phone', 'label' => 'Nomor WhatsApp Aktif', 'type' => 'text', 'is_required' => true, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'INT_SEC_STATUS',
                    'title' => 'Status Penerimaan',
                    'order' => 2,
                    'fields' => [
                        [
                            'name' => 'INT_acceptance_status', 
                            'label' => 'Status penerimaan', 
                            'type' => 'radio', 
                            'is_required' => true, 
                            'options' => ['Sudah diterima', 'Sedang proses seleksi', 'Baru akan mengajukan', 'Belum mendapatkan tempat'],
                            'order' => 1
                        ],
                        
                        // Conditionals for 'Belum mendapatkan tempat'
                        ['name' => 'INT_internship_interest_area', 'label' => 'Bidang Minat', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Belum mendapatkan tempat'], 'order' => 2],
                        ['name' => 'INT_help_notes', 'label' => 'Catatan kebutuhan bantuan penempatan', 'type' => 'textarea', 'is_required' => false, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Belum mendapatkan tempat'], 'order' => 3],
                    ]
                ],
                [
                    'section_code' => 'INT_SEC_COMPANY',
                    'title' => 'Data Perusahaan',
                    'order' => 3,
                    'fields' => [
                        ['name' => 'INT_company_name', 'label' => 'Nama perusahaan/instansi (atau target)', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'not_equals', 'value' => 'Belum mendapatkan tempat'], 'order' => 1],
                        ['name' => 'INT_company_field', 'label' => 'Bidang perusahaan/instansi', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 2],
                        ['name' => 'INT_company_address', 'label' => 'Alamat perusahaan/instansi', 'type' => 'textarea', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 3],
                        ['name' => 'INT_mentor_name', 'label' => 'Nama penanggung jawab/Mentor perusahaan', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 4],
                        ['name' => 'INT_mentor_role', 'label' => 'Jabatan penanggung jawab/Mentor', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 5],
                        ['name' => 'INT_mentor_phone', 'label' => 'Nomor kontak penanggung jawab/Mentor', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 6],
                    ]
                ],
                [
                    'section_code' => 'INT_SEC_POSITION',
                    'title' => 'Posisi dan Pekerjaan',
                    'order' => 4,
                    'fields' => [
                        // Conditional target position for processes or will apply
                        ['name' => 'INT_target_position', 'label' => 'Target posisi/divisi', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'not_equals', 'value' => 'Sudah diterima'], 'order' => 1],
                        
                        // Conditional accepted position
                        ['name' => 'INT_position_or_division', 'label' => 'Posisi atau divisi yang ditempati', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 2],
                        ['name' => 'INT_job_description', 'label' => 'Deskripsi singkat pekerjaan (Job Description)', 'type' => 'textarea', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 3],
                        
                        // Process selection fields
                        ['name' => 'INT_selection_stage', 'label' => 'Tahap seleksi saat ini', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sedang proses seleksi'], 'order' => 4],
                        ['name' => 'INT_selection_result_estimate', 'label' => 'Estimasi pengumuman hasil seleksi', 'type' => 'date', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sedang proses seleksi'], 'order' => 5],

                        // Planning to apply fields
                        ['name' => 'INT_application_plan_date', 'label' => 'Rencana tanggal pengajuan lamaran', 'type' => 'date', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Baru akan mengajukan'], 'order' => 6],
                    ]
                ],
                [
                    'section_code' => 'INT_SEC_SCHEDULE',
                    'title' => 'Jadwal dan Sistem Kerja',
                    'order' => 5,
                    'fields' => [
                        ['name' => 'INT_internship_start_date', 'label' => 'Tanggal mulai magang/kerja praktik', 'type' => 'date', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 1],
                        ['name' => 'INT_internship_end_date', 'label' => 'Tanggal selesai magang/kerja praktik', 'type' => 'date', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 2],
                        
                        ['name' => 'INT_work_system', 'label' => 'Sistem pelaksanaan', 'type' => 'radio', 'is_required' => true, 'options' => ['On-site/WFO', 'Remote/WFH', 'Hybrid'], 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 3],
                        
                        ['name' => 'INT_work_schedule', 'label' => 'Hari dan jam kerja (Cth: Senin–Jumat, 09.00–17.00)', 'type' => 'text', 'is_required' => false, 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 4],
                        
                        ['name' => 'INT_onsite_schedule', 'label' => 'Jadwal On-site', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_work_system', 'operator' => 'equals', 'value' => 'Hybrid'], 'order' => 5],
                        ['name' => 'INT_remote_schedule', 'label' => 'Jadwal Remote', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'INT_work_system', 'operator' => 'equals', 'value' => 'Hybrid'], 'order' => 6],
                    ]
                ],
                [
                    'section_code' => 'INT_SEC_DOCUMENT',
                    'title' => 'Dokumen',
                    'order' => 6,
                    'fields' => [
                        ['name' => 'INT_acceptance_letter_file', 'label' => 'Surat Penerimaan/Acceptance Letter', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf', 'jpg', 'png'], 'conditions' => ['trigger_field_id' => 'INT_acceptance_status', 'operator' => 'equals', 'value' => 'Sudah diterima'], 'order' => 1],
                    ]
                ],
                [
                    'section_code' => 'INT_SEC_DECLARATION',
                    'title' => 'Pernyataan',
                    'order' => 7,
                    'fields' => [
                        ['name' => 'INT_internship_declarations', 'label' => 'Pernyataan mahasiswa', 'type' => 'checkbox', 'is_required' => true, 'options' => ['Saya menyatakan data yang diisi benar.', 'Saya bersedia mengikuti ketentuan Magang/Kerja Praktik Prodi Informatika.', 'Saya bersedia mengisi logbook kegiatan secara berkala.', 'Saya bersedia menyusun laporan akhir magang/kerja praktik.', 'Saya bersedia menjaga nama baik Universitas Pradita selama kegiatan berlangsung.', 'Saya memahami pengajuan ini akan divalidasi oleh Program Studi.'], 'order' => 1],
                    ]
                ],
            ]
        ];
    }
    protected function getThesisRegistration()
    {
        return [
            'form_code' => 'THESIS_REGISTRATION',
            'nama' => 'Pendaftaran Tugas Akhir',
            'jenis_kegiatan' => 'Tugas Akhir',
            'activity_type_slug' => 'tugas-akhir',
            'fase' => 'registration',
            'description' => 'Pendaftaran Pengajuan Proposal Tugas Akhir Mahasiswa',
            'semester' => 'Ganjil',
            'depends_on_form_code' => null,
            'sections' => [
                [
                    'section_code' => 'TA_SEC_IDENTITY',
                    'title' => 'Identitas Mahasiswa',
                    'order' => 1,
                    'fields' => [
                        ['name' => 'TA_nim', 'label' => 'NIM', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'TA_student_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'TA_cohort', 'label' => 'Angkatan', 'type' => 'text', 'is_required' => true, 'order' => 3],
                        ['name' => 'TA_student_phone', 'label' => 'Nomor WhatsApp Aktif', 'type' => 'text', 'is_required' => true, 'order' => 4],
                        ['name' => 'TA_study_focus', 'label' => 'Peminatan', 'type' => 'master_data', 'options' => 'StudyFocus', 'is_required' => true, 'order' => 5],
                    ]
                ],
                [
                    'section_code' => 'TA_SEC_ACADEMIC',
                    'title' => 'Persyaratan Akademik',
                    'order' => 2,
                    'fields' => [
                        ['name' => 'TA_total_credits', 'label' => 'Total SKS Lulus', 'type' => 'number', 'is_required' => true, 'order' => 1],
                        ['name' => 'TA_gpa', 'label' => 'IPK Terakhir', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'TA_transcript_file', 'label' => 'Transkrip Nilai Terakhir (PDF)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 3],
                        ['name' => 'TA_krs_file', 'label' => 'KRS Semester Berjalan (PDF)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'TA_SEC_PROPOSAL',
                    'title' => 'Usulan Tugas Akhir',
                    'order' => 3,
                    'fields' => [
                        ['name' => 'TA_proposed_title', 'label' => 'Usulan Judul Tugas Akhir', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'TA_research_background', 'label' => 'Latar Belakang Masalah (Singkat)', 'type' => 'textarea', 'is_required' => true, 'order' => 2],
                        ['name' => 'TA_research_questions', 'label' => 'Rumusan Masalah', 'type' => 'textarea', 'is_required' => true, 'order' => 3],
                        ['name' => 'TA_proposed_methodology', 'label' => 'Rencana Metode Penyelesaian/Metodologi', 'type' => 'textarea', 'is_required' => true, 'order' => 4],
                        ['name' => 'TA_references', 'label' => 'Referensi Utama (3-5 paper/jurnal referensi)', 'type' => 'textarea', 'is_required' => true, 'order' => 5],
                    ]
                ],
                [
                    'section_code' => 'TA_SEC_SUPERVISOR',
                    'title' => 'Usulan Dosen Pembimbing',
                    'order' => 4,
                    'fields' => [
                        ['name' => 'TA_proposed_supervisor_1', 'label' => 'Usulan Dosen Pembimbing 1', 'type' => 'master_data', 'options' => 'Lecturer', 'is_required' => true, 'order' => 1],
                        ['name' => 'TA_proposed_supervisor_2', 'label' => 'Usulan Dosen Pembimbing 2 (Opsional)', 'type' => 'master_data', 'options' => 'Lecturer', 'is_required' => false, 'order' => 2],
                        
                        ['name' => 'TA_supervisor_approval_status', 'label' => 'Status Komunikasi dengan Calon Pembimbing', 'type' => 'radio', 'is_required' => true, 'options' => ['Sudah berdiskusi dan disetujui', 'Belum berdiskusi', 'Usulan dosen pembimbing dari instansi luar (Co-Supervisor)'], 'order' => 3],
                        
                        ['name' => 'TA_supervisor_approval_file', 'label' => 'Bukti persetujuan/diskusi (Screenshot/Email)', 'type' => 'file', 'is_required' => true, 'allowed_types' => ['pdf', 'png', 'jpg'], 'conditions' => ['trigger_field_id' => 'TA_supervisor_approval_status', 'operator' => 'equals', 'value' => 'Sudah berdiskusi dan disetujui'], 'order' => 4],
                        
                        ['name' => 'TA_supervisor_consultation_notes', 'label' => 'Alasan memilih dosen tersebut / Harapan bimbingan', 'type' => 'textarea', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'TA_supervisor_approval_status', 'operator' => 'equals', 'value' => 'Belum berdiskusi'], 'order' => 5],

                        ['name' => 'TA_external_supervisor_name', 'label' => 'Nama Lengkap Calon Pembimbing Luar beserta Gelar', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'TA_supervisor_approval_status', 'operator' => 'equals', 'value' => 'Usulan dosen pembimbing dari instansi luar (Co-Supervisor)'], 'order' => 6],
                        ['name' => 'TA_external_supervisor_institution', 'label' => 'Asal Instansi/Perusahaan', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'TA_supervisor_approval_status', 'operator' => 'equals', 'value' => 'Usulan dosen pembimbing dari instansi luar (Co-Supervisor)'], 'order' => 7],
                        ['name' => 'TA_external_supervisor_phone', 'label' => 'Nomor Kontak', 'type' => 'text', 'is_required' => true, 'conditions' => ['trigger_field_id' => 'TA_supervisor_approval_status', 'operator' => 'equals', 'value' => 'Usulan dosen pembimbing dari instansi luar (Co-Supervisor)'], 'order' => 8],
                    ]
                ],
                [
                    'section_code' => 'TA_SEC_DOCUMENT',
                    'title' => 'Dokumen Pendaftaran',
                    'order' => 5,
                    'fields' => [
                        ['name' => 'TA_proposal_file', 'label' => 'Dokumen Proposal Tugas Akhir (Sesuai Template)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 5, 'allowed_types' => ['pdf'], 'order' => 1],
                        ['name' => 'TA_plagiarism_statement_file', 'label' => 'Formulir Bebas Plagiarisme', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 2],
                    ]
                ],
                [
                    'section_code' => 'TA_SEC_DECLARATION',
                    'title' => 'Pernyataan',
                    'order' => 6,
                    'fields' => [
                        ['name' => 'TA_declarations', 'label' => 'Pernyataan mahasiswa', 'type' => 'checkbox', 'is_required' => true, 'options' => ['Saya menyatakan seluruh data dan dokumen yang dilampirkan adalah benar.', 'Saya bersedia menerima alokasi Dosen Pembimbing yang ditetapkan oleh Program Studi jika usulan saya tidak disetujui.', 'Saya berkomitmen untuk menyelesaikan Tugas Akhir sesuai target waktu yang ditentukan.', 'Proposal yang diajukan bebas dari unsur plagiarisme.'], 'order' => 1],
                    ]
                ],
            ]
        ];
    }
    protected function getThesisDefenseRegistration()
    {
        return [
            'form_code' => 'THESIS_DEFENSE_REGISTRATION',
            'nama' => 'Pendaftaran Sidang Tugas Akhir',
            'jenis_kegiatan' => 'Tugas Akhir',
            'activity_type_slug' => 'tugas-akhir',
            'fase' => 'reporting',
            'description' => 'Pendaftaran Sidang/Pendadaran Tugas Akhir',
            'semester' => 'Ganjil',
            'depends_on_form_code' => 'THESIS_REGISTRATION', // Relational dependency
            'sections' => [
                [
                    'section_code' => 'TADEF_SEC_IDENTITY',
                    'title' => 'Identitas Mahasiswa',
                    'order' => 1,
                    'fields' => [
                        ['name' => 'TADEF_nim', 'label' => 'NIM', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'TADEF_student_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'TADEF_cohort', 'label' => 'Angkatan', 'type' => 'text', 'is_required' => true, 'order' => 3],
                        ['name' => 'TADEF_student_phone', 'label' => 'Nomor WhatsApp Aktif', 'type' => 'text', 'is_required' => true, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'TADEF_SEC_DATA',
                    'title' => 'Data Tugas Akhir',
                    'order' => 2,
                    'fields' => [
                        ['name' => 'TADEF_final_title', 'label' => 'Judul Final Tugas Akhir (Bahasa Indonesia)', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'TADEF_final_title_en', 'label' => 'Judul Final Tugas Akhir (Bahasa Inggris)', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'TADEF_supervisor_1', 'label' => 'Dosen Pembimbing 1 (Fix)', 'type' => 'master_data', 'options' => 'Lecturer', 'is_required' => true, 'order' => 3],
                        ['name' => 'TADEF_supervisor_2', 'label' => 'Dosen Pembimbing 2 (Jika ada)', 'type' => 'master_data', 'options' => 'Lecturer', 'is_required' => false, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'TADEF_SEC_ACADEMIC',
                    'title' => 'Persyaratan Akademik Sidang',
                    'order' => 3,
                    'fields' => [
                        ['name' => 'TADEF_total_credits', 'label' => 'Total SKS Lulus (Minimal 138)', 'type' => 'number', 'is_required' => true, 'order' => 1],
                        ['name' => 'TADEF_toefl_score', 'label' => 'Skor TOEFL (Minimal 450)', 'type' => 'number', 'is_required' => true, 'order' => 2],
                        ['name' => 'TADEF_toefl_certificate', 'label' => 'Sertifikat TOEFL', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf', 'jpg'], 'order' => 3],
                        ['name' => 'TADEF_library_clearance_file', 'label' => 'Surat Bebas Pustaka', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 4],
                        ['name' => 'TADEF_logbook_file', 'label' => 'Logbook Bimbingan (Minimal 8 kali)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 5],
                    ]
                ],
                [
                    'section_code' => 'TADEF_SEC_APPROVAL',
                    'title' => 'Persetujuan Pembimbing',
                    'order' => 4,
                    'fields' => [
                        ['name' => 'TADEF_supervisor_approval_defense_file', 'label' => 'Surat Persetujuan Maju Sidang (ACC Pembimbing)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 1],
                    ]
                ],
                [
                    'section_code' => 'TADEF_SEC_DOCUMENT',
                    'title' => 'Dokumen Sidang',
                    'order' => 5,
                    'fields' => [
                        ['name' => 'TADEF_final_draft_file', 'label' => 'Buku Tugas Akhir (Draft Final)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 10, 'allowed_types' => ['pdf'], 'order' => 1],
                        ['name' => 'TADEF_journal_draft_file', 'label' => 'Draft Jurnal Publikasi', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 5, 'allowed_types' => ['pdf'], 'order' => 2],
                        ['name' => 'TADEF_turnitin_result_file', 'label' => 'Hasil Cek Turnitin (Maksimal 25%)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 5, 'allowed_types' => ['pdf'], 'order' => 3],
                        ['name' => 'TADEF_application_demo_link', 'label' => 'Tautan Aplikasi / Demo Video (Jika ada)', 'type' => 'url', 'is_required' => false, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'TADEF_SEC_DECLARATION',
                    'title' => 'Pernyataan',
                    'order' => 6,
                    'fields' => [
                        ['name' => 'TADEF_defense_declarations', 'label' => 'Pernyataan mahasiswa', 'type' => 'checkbox', 'is_required' => true, 'options' => ['Saya menyatakan syarat minimum kehadiran kuliah dan nilai D maksimal 2 mata kuliah telah terpenuhi.', 'Saya menyatakan dokumen yang diunggah adalah versi final yang telah disetujui.', 'Saya siap menerima sanksi pembatalan kelulusan jika ditemukan fabrikasi data atau plagiarisme.', 'Saya bersedia hadir 30 menit sebelum jadwal sidang dimulai.'], 'order' => 1],
                    ]
                ],
            ]
        ];
    }
    protected function getCourseConversionRegistration()
    {
        return [
            'form_code' => 'COURSE_CONVERSION_REGISTRATION',
            'nama' => 'Pendaftaran Konversi Mata Kuliah',
            'jenis_kegiatan' => 'Konversi Mata Kuliah',
            'activity_type_slug' => 'konversi-mk',
            'fase' => 'registration',
            'description' => 'Pengajuan Konversi SKS dari kegiatan MBKM, Sertifikasi, atau Prestasi',
            'semester' => 'Ganjil',
            'depends_on_form_code' => null,
            'sections' => [
                [
                    'section_code' => 'CONV_SEC_IDENTITY',
                    'title' => 'Identitas Mahasiswa',
                    'order' => 1,
                    'fields' => [
                        ['name' => 'CONV_nim', 'label' => 'NIM', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'CONV_student_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'CONV_cohort', 'label' => 'Angkatan', 'type' => 'text', 'is_required' => true, 'order' => 3],
                        ['name' => 'CONV_student_phone', 'label' => 'Nomor WhatsApp Aktif', 'type' => 'text', 'is_required' => true, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'CONV_SEC_SOURCE',
                    'title' => 'Asal Kegiatan',
                    'order' => 2,
                    'fields' => [
                        ['name' => 'CONV_activity_source', 'label' => 'Jenis Kegiatan Asal', 'type' => 'radio', 'is_required' => true, 'options' => ['MBKM Kampus Merdeka', 'Sertifikasi Internasional', 'Lomba/Kejuaraan', 'Lainnya'], 'order' => 1],
                        ['name' => 'CONV_activity_name', 'label' => 'Nama Kegiatan/Sertifikasi/Lomba', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'CONV_organizer_name', 'label' => 'Penyelenggara', 'type' => 'text', 'is_required' => true, 'order' => 3],
                        ['name' => 'CONV_activity_duration', 'label' => 'Durasi Kegiatan (Bulan)', 'type' => 'number', 'is_required' => true, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'CONV_SEC_COURSES',
                    'title' => 'Daftar Mata Kuliah yang Diusulkan',
                    'order' => 3,
                    'fields' => [
                        [
                            'name' => 'CONV_courses_to_convert', 
                            'label' => 'Daftar Mata Kuliah', 
                            'type' => 'repeater', 
                            'is_required' => true,
                            'options' => json_encode([
                                'fields' => [
                                    ['name' => 'course_id', 'label' => 'Mata Kuliah', 'type' => 'master_data', 'options' => 'Course'],
                                ]
                            ]),
                            'order' => 1
                        ],
                    ]
                ],
                [
                    'section_code' => 'CONV_SEC_DOCUMENT',
                    'title' => 'Dokumen Bukti',
                    'order' => 4,
                    'fields' => [
                        ['name' => 'CONV_activity_acceptance_file', 'label' => 'Surat Penerimaan/LoA/Bukti Kepesertaan Asli', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 1],
                    ]
                ],
                [
                    'section_code' => 'CONV_SEC_DECLARATION',
                    'title' => 'Pernyataan',
                    'order' => 5,
                    'fields' => [
                        ['name' => 'CONV_declarations', 'label' => 'Pernyataan mahasiswa', 'type' => 'checkbox', 'is_required' => true, 'options' => ['Saya menyatakan seluruh dokumen adalah sah.', 'Saya mengerti bahwa usulan mata kuliah ini dapat disetujui sebagian atau seluruhnya oleh Kaprodi.'], 'order' => 1],
                    ]
                ],
            ]
        ];
    }
    protected function getCourseConversionResult()
    {
        return [
            'form_code' => 'COURSE_CONVERSION_RESULT',
            'nama' => 'Pengumpulan Hasil Konversi Mata Kuliah',
            'jenis_kegiatan' => 'Konversi Mata Kuliah',
            'activity_type_slug' => 'konversi-mk',
            'fase' => 'reporting',
            'description' => 'Pelaporan Akhir dan Pengumpulan Bukti Nilai/Sertifikat Konversi MK',
            'semester' => 'Ganjil',
            'depends_on_form_code' => 'COURSE_CONVERSION_REGISTRATION', // Relational dependency
            'sections' => [
                [
                    'section_code' => 'CONVRES_SEC_IDENTITY',
                    'title' => 'Identitas Mahasiswa',
                    'order' => 1,
                    'fields' => [
                        ['name' => 'CONVRES_nim', 'label' => 'NIM', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'CONVRES_student_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'CONVRES_cohort', 'label' => 'Angkatan', 'type' => 'text', 'is_required' => true, 'order' => 3],
                        ['name' => 'CONVRES_student_phone', 'label' => 'Nomor WhatsApp Aktif', 'type' => 'text', 'is_required' => true, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'CONVRES_SEC_REPORT',
                    'title' => 'Laporan dan Penilaian',
                    'order' => 2,
                    'fields' => [
                        ['name' => 'CONVRES_final_report_file', 'label' => 'Laporan Akhir Kegiatan (PDF)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 10, 'allowed_types' => ['pdf'], 'order' => 1],
                        ['name' => 'CONVRES_certificate_file', 'label' => 'Sertifikat Penyelesaian Asli (PDF/JPG)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf', 'jpg', 'png'], 'order' => 2],
                        ['name' => 'CONVRES_grading_document_file', 'label' => 'Dokumen Penilaian/Transkrip dari Mitra', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 3],
                    ]
                ],
                [
                    'section_code' => 'CONVRES_SEC_EVALUATION',
                    'title' => 'Evaluasi Kegiatan',
                    'order' => 3,
                    'fields' => [
                        ['name' => 'CONVRES_activity_evaluation', 'label' => 'Evaluasi singkat mengenai kegiatan', 'type' => 'textarea', 'is_required' => true, 'order' => 1],
                        ['name' => 'CONVRES_recommendation_for_juniors', 'label' => 'Rekomendasi/Pesan untuk adik tingkat yang ingin mengikuti kegiatan serupa', 'type' => 'textarea', 'is_required' => true, 'order' => 2],
                    ]
                ],
                [
                    'section_code' => 'CONVRES_SEC_DECLARATION',
                    'title' => 'Pernyataan',
                    'order' => 4,
                    'fields' => [
                        ['name' => 'CONVRES_declarations', 'label' => 'Pernyataan mahasiswa', 'type' => 'checkbox', 'is_required' => true, 'options' => ['Saya menyatakan seluruh dokumen penilaian dan sertifikat yang dilampirkan adalah sah dan dapat dipertanggungjawabkan.'], 'order' => 1],
                    ]
                ],
            ]
        ];
    }
    protected function getInternshipDefenseRegistration()
    {
        return [
            'form_code' => 'INTERNSHIP_DEFENSE_REGISTRATION',
            'nama' => 'Pendaftaran Sidang Magang/Kerja Praktik',
            'jenis_kegiatan' => 'Magang / Kerja Praktik',
            'activity_type_slug' => 'magang-kp',
            'fase' => 'reporting',
            'description' => 'Pendaftaran Sidang/Presentasi Akhir Magang',
            'semester' => 'Ganjil',
            'depends_on_form_code' => 'INTERNSHIP_REGISTRATION', // Relational dependency
            'sections' => [
                [
                    'section_code' => 'INTDEF_SEC_IDENTITY',
                    'title' => 'Identitas Mahasiswa',
                    'order' => 1,
                    'fields' => [
                        ['name' => 'INTDEF_nim', 'label' => 'NIM', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'INTDEF_student_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'INTDEF_cohort', 'label' => 'Angkatan', 'type' => 'text', 'is_required' => true, 'order' => 3],
                        ['name' => 'INTDEF_student_phone', 'label' => 'Nomor WhatsApp Aktif', 'type' => 'text', 'is_required' => true, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'INTDEF_SEC_DATA',
                    'title' => 'Data Pelaksanaan',
                    'order' => 2,
                    'fields' => [
                        ['name' => 'INTDEF_company_name', 'label' => 'Nama perusahaan/instansi', 'type' => 'text', 'is_required' => true, 'order' => 1],
                        ['name' => 'INTDEF_mentor_name', 'label' => 'Nama Mentor perusahaan', 'type' => 'text', 'is_required' => true, 'order' => 2],
                        ['name' => 'INTDEF_internship_start_date', 'label' => 'Tanggal mulai magang', 'type' => 'date', 'is_required' => true, 'order' => 3],
                        ['name' => 'INTDEF_internship_end_date', 'label' => 'Tanggal selesai magang', 'type' => 'date', 'is_required' => true, 'order' => 4],
                    ]
                ],
                [
                    'section_code' => 'INTDEF_SEC_ACADEMIC',
                    'title' => 'Persyaratan Akademik Sidang',
                    'order' => 3,
                    'fields' => [
                        ['name' => 'INTDEF_total_credits', 'label' => 'Total SKS Lulus (Minimal 90 SKS)', 'type' => 'number', 'is_required' => true, 'order' => 1],
                        ['name' => 'INTDEF_krs_file', 'label' => 'KRS Berjalan (Mencantumkan MK Magang)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 2],
                    ]
                ],
                [
                    'section_code' => 'INTDEF_SEC_APPROVAL',
                    'title' => 'Persetujuan Pembimbing dan Mitra',
                    'order' => 4,
                    'fields' => [
                        ['name' => 'INTDEF_mentor_evaluation_file', 'label' => 'Lembar Penilaian dari Mentor (Diisi dan dicap perusahaan)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf', 'jpg'], 'order' => 1],
                        ['name' => 'INTDEF_supervisor_approval_defense_file', 'label' => 'Surat Persetujuan Maju Sidang (ACC Dosen Pembimbing)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 2, 'allowed_types' => ['pdf'], 'order' => 2],
                    ]
                ],
                [
                    'section_code' => 'INTDEF_SEC_REPORT',
                    'title' => 'Dokumen Laporan',
                    'order' => 5,
                    'fields' => [
                        ['name' => 'INTDEF_logbook_file', 'label' => 'Logbook Kegiatan Selama Magang (Ditandatangani Mentor)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 5, 'allowed_types' => ['pdf'], 'order' => 1],
                        ['name' => 'INTDEF_internship_report_file', 'label' => 'Laporan Akhir Magang (Draft Final)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 10, 'allowed_types' => ['pdf'], 'order' => 2],
                        ['name' => 'INTDEF_turnitin_result_file', 'label' => 'Hasil Cek Turnitin (Maksimal 25%)', 'type' => 'file', 'is_required' => true, 'max_size_mb' => 5, 'allowed_types' => ['pdf'], 'order' => 3],
                    ]
                ],
                [
                    'section_code' => 'INTDEF_SEC_DECLARATION',
                    'title' => 'Pernyataan',
                    'order' => 6,
                    'fields' => [
                        ['name' => 'INTDEF_defense_declarations', 'label' => 'Pernyataan mahasiswa', 'type' => 'checkbox', 'is_required' => true, 'options' => ['Saya menyatakan nilai evaluasi mentor asli dan dapat dipertanggungjawabkan.', 'Saya telah memenuhi masa kegiatan magang sesuai aturan Program Studi.'], 'order' => 1],
                    ]
                ],
            ]
        ];
    }
}
