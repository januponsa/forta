<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Document Assets - master record per asset
        Schema::create('document_assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('asset_type'); // logo, signature, stamp, photo, image
            $table->string('mime_type');
            $table->string('status')->default('active'); // active, inactive

            // Ownership
            $table->string('owner_type')->nullable(); // user, lecturer, system
            $table->unsignedBigInteger('owner_id')->nullable();

            // Default display dimensions
            $table->integer('default_width')->nullable();
            $table->integer('default_height')->nullable();

            // Active version
            $table->unsignedBigInteger('active_version_id')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Document Asset Versions - immutable snapshots
        Schema::create('document_asset_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_asset_id')->constrained('document_assets')->cascadeOnDelete();
            $table->integer('version_number')->default(1);

            // File paths
            $table->string('original_path');    // file asli, tidak pernah ditimpa
            $table->string('processed_path')->nullable(); // hasil crop/resize
            $table->string('thumbnail_path')->nullable();

            // Original file metadata
            $table->integer('original_width');
            $table->integer('original_height');
            $table->decimal('aspect_ratio', 10, 6);
            $table->integer('file_size'); // bytes
            $table->string('file_format'); // png, jpg, svg, etc.
            $table->boolean('has_transparency')->default(false);

            // Processing metadata
            $table->json('crop_data')->nullable(); // {x, y, width, height}
            $table->integer('rotation')->default(0); // degrees
            $table->boolean('flip_horizontal')->default(false);
            $table->boolean('flip_vertical')->default(false);
            $table->decimal('opacity', 3, 2)->default(1.00);
            $table->string('object_fit')->default('contain'); // contain, cover

            // Processed dimensions
            $table->integer('processed_width')->nullable();
            $table->integer('processed_height')->nullable();

            $table->text('change_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // FK after both tables exist
        Schema::table('document_assets', function (Blueprint $table) {
            $table->foreign('active_version_id')
                  ->references('id')
                  ->on('document_asset_versions')
                  ->nullOnDelete();
        });

        // Now add the proper FK on letterhead_versions.logo_asset_id
        Schema::table('letterhead_versions', function (Blueprint $table) {
            $table->dropColumn('logo_asset_id');
        });
        Schema::table('letterhead_versions', function (Blueprint $table) {
            $table->foreignId('logo_asset_id')->nullable()->after('status')
                  ->constrained('document_assets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('letterhead_versions', function (Blueprint $table) {
            $table->dropForeign(['logo_asset_id']);
            $table->dropColumn('logo_asset_id');
        });
        Schema::table('letterhead_versions', function (Blueprint $table) {
            $table->string('logo_asset_id')->nullable()->after('status');
        });

        Schema::table('document_assets', function (Blueprint $table) {
            $table->dropForeign(['active_version_id']);
        });
        Schema::dropIfExists('document_asset_versions');
        Schema::dropIfExists('document_assets');
    }
};
