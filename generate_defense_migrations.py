import sys

file_path = r'c:\Users\userJ\Documents\fortain\database\migrations\2026_07_14_123123_create_defense_management_tables.php'

migration_code = """<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add signature_path and user_id to lecturers
        Schema::table('lecturers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('signature_path')->nullable()->after('is_active');
        });

        // 1. Defense Cases (Master record per student defense)
        Schema::create('defense_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('defense_type')->default('internship_defense'); // internship_defense, thesis_proposal, etc.
            $table->string('semester');
            $table->string('status')->default('registered');
            // 'registered', 'documents_verified', 'waiting_schedule', 'scheduled', 
            // 'waiting_scores', 'ready_to_finalize', 'revision_required', 'completed', 'failed'
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('final_grade', 2)->nullable(); // A, A-, B+, etc.
            $table->string('final_decision')->nullable(); // Lulus, Lulus dengan Revisi, Tidak Lulus
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users');
            $table->json('metadata')->nullable(); // Company name, mentor name, etc.
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Defense Schedules
        Schema::create('defense_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_case_id')->constrained('defense_cases')->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_or_link')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Defense Assignments (Lecturer roles)
        Schema::create('defense_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_case_id')->constrained('defense_cases')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained('lecturers')->cascadeOnDelete();
            $table->string('role'); // supervisor, examiner, co_examiner
            $table->timestamps();
        });

        // 4. Rubric Structure (Versions, Sections, Items)
        Schema::create('rubric_versions', function (Blueprint $table) {
            $table->id();
            $table->string('defense_type');
            $table->string('role'); // supervisor, examiner, mentor
            $table->string('version_name'); // e.g. "v1.0"
            $table->boolean('is_active')->default(true);
            $table->decimal('weight_percentage', 5, 2)->default(0); // e.g. 30, 40
            $table->timestamps();
        });

        Schema::create('rubric_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_version_id')->constrained('rubric_versions')->cascadeOnDelete();
            $table->string('name'); // e.g. "A. Ketekunan Mahasiswa"
            $table->integer('max_score')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('rubric_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_section_id')->constrained('rubric_sections')->cascadeOnDelete();
            $table->string('code')->nullable(); // e.g. A1, A2
            $table->text('description');
            $table->integer('max_score');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // 5. Assessments & Scores
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_case_id')->constrained('defense_cases')->cascadeOnDelete();
            $table->foreignId('rubric_version_id')->constrained('rubric_versions')->cascadeOnDelete();
            // Assessor can be lecturer (for supervisor/examiner) or user (admin who inputs mentor score)
            $table->unsignedBigInteger('assessor_id')->nullable();
            $table->string('assessor_type'); // 'lecturer', 'admin'
            $table->string('assessor_role'); // 'supervisor', 'examiner', 'mentor'
            
            $table->decimal('total_score', 5, 2)->nullable();
            $table->string('status')->default('draft'); // draft, final
            $table->timestamp('finalized_at')->nullable();
            
            // For examiner specific
            $table->string('originality_status')->nullable(); // Tidak Ada Indikasi Pelanggaran, Perlu Pemeriksaan, Terbukti Plagiarisme
            $table->text('originality_notes')->nullable();

            $table->text('general_notes')->nullable();
            
            $table->timestamps();
        });

        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('rubric_item_id')->constrained('rubric_items')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->timestamps();
        });

        // 6. Defense Suggestions (F6)
        Schema::create('defense_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_case_id')->constrained('defense_cases')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained('lecturers')->cascadeOnDelete();
            $table->string('lecturer_role'); // supervisor, examiner
            $table->string('category'); // Penyempurnaan Alat, Laporan, dll
            $table->text('suggestion');
            $table->string('priority')->default('normal'); // high, normal, low
            $table->string('status')->default('Belum Dikerjakan'); // Belum Dikerjakan, Sedang Dikerjakan, Sudah Diperbaiki, Disetujui
            $table->text('student_response')->nullable();
            $table->string('revision_file_path')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // 7. Generated Documents (F1 - F6)
        Schema::create('generated_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_case_id')->constrained('defense_cases')->cascadeOnDelete();
            $table->string('document_type'); // F1, F2, F3, dll
            $table->string('file_path');
            $table->string('original_name');
            $table->integer('version')->default(1);
            $table->string('checksum')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 8. Defense Histories (Audit Log)
        Schema::create('defense_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_case_id')->constrained('defense_cases')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // scheduled, finalized_f3, generated_pdf, reopened_score
            $table->text('description')->nullable();
            $table->json('before_state')->nullable();
            $table->json('after_state')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defense_histories');
        Schema::dropIfExists('generated_documents');
        Schema::dropIfExists('defense_suggestions');
        Schema::dropIfExists('assessment_scores');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('rubric_items');
        Schema::dropIfExists('rubric_sections');
        Schema::dropIfExists('rubric_versions');
        Schema::dropIfExists('defense_assignments');
        Schema::dropIfExists('defense_schedules');
        Schema::dropIfExists('defense_cases');
        
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'signature_path']);
        });
    }
};
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(migration_code)
