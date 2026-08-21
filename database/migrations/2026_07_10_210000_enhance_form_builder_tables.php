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
        // 1. Create form_sections table
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 2. Create question_banks table
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('type');
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('placeholder')->nullable();
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('max_files')->nullable();
            $table->integer('max_size_mb')->nullable();
            $table->json('allowed_types')->nullable();
            $table->timestamps();
        });

        // 3. Modify form_fields table
        Schema::table('form_fields', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->constrained('form_sections')->nullOnDelete();
            $table->text('description')->nullable();
            $table->json('validation_rules')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('default_value')->nullable();
            $table->json('conditions')->nullable();
            $table->string('type')->change(); // change enum to string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
            $table->dropColumn(['section_id', 'description', 'validation_rules', 'is_active', 'default_value', 'conditions']);
        });

        Schema::dropIfExists('question_banks');
        Schema::dropIfExists('form_sections');
    }
};
