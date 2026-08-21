<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Document Instances - one per generated document (per pengajuan/sidang)
        Schema::create('document_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')->constrained('document_templates')->cascadeOnDelete();
            $table->unsignedBigInteger('template_version_id')->nullable();
            $table->unsignedBigInteger('letterhead_version_id')->nullable();

            // Polymorphic source (InternshipLetterRequest, DefenseCase, etc.)
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');

            // Status
            $table->string('status')->default('draft');
            // draft, review, ready, final, revised, cancelled

            // Snapshot data at time of generation
            $table->json('data_snapshot')->nullable();
            $table->json('asset_snapshots')->nullable(); // [{asset_id, version_id, path}]
            $table->json('override_data')->nullable();    // merged overrides

            // Final file
            $table->string('file_path')->nullable();
            $table->string('file_checksum')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();

            // Revision tracking
            $table->unsignedBigInteger('previous_instance_id')->nullable();
            $table->integer('revision_number')->default(1);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('template_version_id')
                  ->references('id')->on('document_template_versions')->nullOnDelete();
            $table->foreign('letterhead_version_id')
                  ->references('id')->on('letterhead_versions')->nullOnDelete();
            $table->foreign('previous_instance_id')
                  ->references('id')->on('document_instances')->nullOnDelete();

            $table->index(['source_type', 'source_id']);
        });

        // 2. Document Instance Overrides - per-field overrides
        Schema::create('document_instance_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_instance_id')
                  ->constrained('document_instances')->cascadeOnDelete();

            $table->string('field_key');        // e.g. "body_paragraph_2", "signatory_name"
            $table->text('original_value')->nullable();
            $table->text('override_value')->nullable();
            $table->string('override_type')->default('text'); // text, html, position, asset
            $table->text('reason')->nullable();

            $table->foreignId('overridden_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Document Histories - audit log for document actions
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();

            // Polymorphic target
            $table->string('target_type'); // DocumentTemplate, LetterheadMaster, DocumentInstance, DocumentAsset
            $table->unsignedBigInteger('target_id');

            $table->string('action'); // created, edited, version_created, published, archived, overridden, finalized, downloaded, regenerated, asset_cropped, asset_resized, signature_used
            $table->text('description')->nullable();

            $table->json('before_state')->nullable();
            $table->json('after_state')->nullable();

            // Version references
            $table->unsignedBigInteger('template_version_id')->nullable();
            $table->unsignedBigInteger('asset_version_id')->nullable();
            $table->unsignedBigInteger('document_instance_id')->nullable();

            $table->text('reason')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_histories');
        Schema::dropIfExists('document_instance_overrides');
        Schema::dropIfExists('document_instances');
    }
};
