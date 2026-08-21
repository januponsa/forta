<?php

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
        Schema::table('forms', function (Blueprint $table) {
            if (!Schema::hasColumn('forms', 'form_code')) {
                $table->string('form_code')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('forms', 'depends_on_form_id')) {
                $table->foreignId('depends_on_form_id')->nullable()->after('form_code')->constrained('forms')->nullOnDelete();
            }
        });

        Schema::table('form_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('form_sections', 'section_code')) {
                $table->string('section_code')->nullable()->after('form_id');
                // Removed unique index since existing rows might have null
                $table->unique(['form_id', 'section_code']);
            }
        });

        Schema::table('form_fields', function (Blueprint $table) {
            if (!Schema::hasColumn('form_fields', 'name')) {
                $table->string('name')->nullable()->after('form_id');
                // Unique per form
                $table->unique(['form_id', 'name']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropUnique(['form_id', 'name']);
            $table->dropColumn('name');
        });

        Schema::table('form_sections', function (Blueprint $table) {
            $table->dropUnique(['form_id', 'section_code']);
            $table->dropColumn('section_code');
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeign(['depends_on_form_id']);
            $table->dropColumn(['depends_on_form_id', 'form_code']);
        });
    }
};
