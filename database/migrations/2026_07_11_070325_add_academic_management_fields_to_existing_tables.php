<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('users', 'status_akun')) {
                $table->string('status_akun')->default('Login Diizinkan');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('students', 'status_akademik')) {
                $table->string('status_akademik')->default('Aktif');
            }
            if (!Schema::hasColumn('students', 'status_akun')) {
                $table->string('status_akun')->default('Login Diizinkan');
            }
        });

        Schema::table('forms', function (Blueprint $table) {
            if (!Schema::hasColumn('forms', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('forms', 'version')) {
                $table->unsignedInteger('version')->default(1);
            }
            if (!Schema::hasColumn('forms', 'parent_form_id')) {
                $table->foreignId('parent_form_id')->nullable()->constrained('forms')->nullOnDelete();
            }
        });

        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'field_review_statuses')) {
                $table->json('field_review_statuses')->nullable();
            }
        });

        Schema::table('submission_files', function (Blueprint $table) {
            if (!Schema::hasColumn('submission_files', 'review_status')) {
                $table->string('review_status')->nullable();
            }
            if (!Schema::hasColumn('submission_files', 'review_note')) {
                $table->text('review_note')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('status_akun');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['status_akademik', 'status_akun']);
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeign(['parent_form_id']);
            $table->dropColumn(['parent_form_id', 'version']);
            $table->dropSoftDeletes();
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('field_review_statuses');
        });

        Schema::table('submission_files', function (Blueprint $table) {
            $table->dropColumn(['review_status', 'review_note']);
        });
    }
};
