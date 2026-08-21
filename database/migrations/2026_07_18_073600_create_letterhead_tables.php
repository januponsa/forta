<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Letterhead Masters - identitas institusi
        Schema::create('letterhead_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // "Kop Surat Program Studi Informatika"
            $table->string('code')->unique();      // "kop_prodi_if"
            $table->string('unit')->nullable();     // "Program Studi Informatika"

            // Identitas Institusi
            $table->string('university_name')->default('Universitas Pradita');
            $table->string('faculty')->nullable();
            $table->string('study_program')->nullable();
            $table->string('campus_address')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();

            // Status & Versioning
            $table->string('status')->default('draft'); // draft, published, inactive, archived
            $table->unsignedBigInteger('active_version_id')->nullable();
            $table->date('effective_date')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Letterhead Versions - snapshot versi kop surat
        Schema::create('letterhead_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letterhead_master_id')->constrained('letterhead_masters')->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('status')->default('draft'); // draft, published, inactive

            // Header visual components
            $table->string('logo_asset_id')->nullable(); // FK to document_assets (set after asset table created)
            $table->text('header_html')->nullable();      // rendered header HTML (for flow editor fallback)
            $table->json('header_layout')->nullable();     // structured JSON layout for canvas

            // Header dimensions
            $table->decimal('header_height', 8, 2)->default(100); // px
            $table->string('separator_style')->default('solid');   // solid, double, none
            $table->integer('separator_width')->default(2);        // px
            $table->string('separator_color')->default('#000000');

            // Footer
            $table->text('footer_html')->nullable();
            $table->json('footer_layout')->nullable();
            $table->decimal('footer_height', 8, 2)->default(30);

            // Margins (default for documents using this kop)
            $table->decimal('margin_top', 8, 2)->default(25);
            $table->decimal('margin_bottom', 8, 2)->default(25);
            $table->decimal('margin_left', 8, 2)->default(25);
            $table->decimal('margin_right', 8, 2)->default(25);

            $table->text('change_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Add FK after both tables exist
        Schema::table('letterhead_masters', function (Blueprint $table) {
            $table->foreign('active_version_id')
                  ->references('id')
                  ->on('letterhead_versions')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('letterhead_masters', function (Blueprint $table) {
            $table->dropForeign(['active_version_id']);
        });
        Schema::dropIfExists('letterhead_versions');
        Schema::dropIfExists('letterhead_masters');
    }
};
