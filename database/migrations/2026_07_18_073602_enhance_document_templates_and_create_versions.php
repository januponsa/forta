<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ALTER document_templates - add versioning, purpose, status columns
        Schema::table('document_templates', function (Blueprint $table) {
            $table->string('document_purpose')->nullable()->unique()->after('type');
            $table->string('category')->nullable()->after('document_purpose');
            $table->string('editor_type')->default('flow')->after('category'); // flow, canvas, overlay
            $table->string('status')->default('draft')->after('editor_type');
            $table->unsignedBigInteger('active_version_id')->nullable()->after('status');
            $table->foreignId('letterhead_version_id')->nullable()->after('active_version_id')
                  ->constrained('letterhead_versions')->nullOnDelete();
            $table->date('effective_date')->nullable()->after('letterhead_version_id');
            $table->foreignId('created_by')->nullable()->after('margin_right')
                  ->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')
                  ->constrained('users')->nullOnDelete();
            $table->softDeletes();
        });

        // 2. Template Versions
        Schema::create('document_template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')->constrained('document_templates')->cascadeOnDelete();
            $table->integer('version_number')->default(1);
            $table->string('status')->default('draft'); // draft, review, published, inactive

            // Content (for flow editor)
            $table->text('header_html')->nullable();
            $table->longText('body_html')->nullable();
            $table->text('footer_html')->nullable();

            // Layout (for canvas editor - structured JSON)
            $table->json('canvas_layout')->nullable();

            // Page settings
            $table->string('paper_size')->default('A4');
            $table->string('orientation')->default('portrait');
            $table->decimal('margin_top', 8, 2)->default(25);
            $table->decimal('margin_bottom', 8, 2)->default(25);
            $table->decimal('margin_left', 8, 2)->default(25);
            $table->decimal('margin_right', 8, 2)->default(25);

            // Kop surat snapshot
            $table->foreignId('letterhead_version_id')->nullable()
                  ->constrained('letterhead_versions')->nullOnDelete();

            // Signatories
            $table->json('signatory_config')->nullable(); // [{signatory_id, position, ...}]

            $table->text('change_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // FK for active_version_id on document_templates
        Schema::table('document_templates', function (Blueprint $table) {
            $table->foreign('active_version_id')
                  ->references('id')
                  ->on('document_template_versions')
                  ->nullOnDelete();
        });

        // 3. Document Elements (for canvas editor)
        Schema::create('document_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_version_id')
                  ->constrained('document_template_versions')->cascadeOnDelete();
            $table->string('element_type'); // text, placeholder, rich_text, image, signature, stamp, line, box, table, dynamic_table, date, letter_number, qr_code, page_number, header, footer, page_break
            $table->integer('page_number')->default(1);

            // Position & dimensions
            $table->decimal('x', 10, 2)->default(0);
            $table->decimal('y', 10, 2)->default(0);
            $table->decimal('width', 10, 2)->default(100);
            $table->decimal('height', 10, 2)->default(30);
            $table->integer('rotation')->default(0);
            $table->decimal('opacity', 3, 2)->default(1.00);

            // Alignment & spacing
            $table->string('text_align')->default('left');
            $table->decimal('padding', 8, 2)->default(0);
            $table->decimal('margin_el', 8, 2)->default(0);
            $table->string('border')->nullable(); // e.g. "1px solid #000"
            $table->integer('z_index')->default(0);

            // State
            $table->boolean('locked')->default(false);
            $table->boolean('visible')->default(true);
            $table->string('condition')->nullable(); // conditional visibility expression

            // Content
            $table->text('content')->nullable();           // static text or HTML
            $table->string('placeholder_key')->nullable(); // e.g. "nama_mahasiswa"
            $table->json('properties')->nullable();        // font_size, font_weight, color, etc.

            // Asset reference
            $table->foreignId('asset_id')->nullable()
                  ->constrained('document_assets')->nullOnDelete();
            $table->foreignId('asset_version_id')->nullable()
                  ->constrained('document_asset_versions')->nullOnDelete();

            // For dynamic tables
            $table->string('data_source')->nullable();   // e.g. "rubric_examiner", "suggestions"
            $table->json('table_columns')->nullable();    // [{key, label, width}]

            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_elements');

        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropForeign(['active_version_id']);
        });
        Schema::dropIfExists('document_template_versions');

        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropForeign(['letterhead_version_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'document_purpose', 'category', 'editor_type', 'status',
                'active_version_id', 'letterhead_version_id', 'effective_date',
                'created_by', 'updated_by', 'deleted_at'
            ]);
        });
    }
};
