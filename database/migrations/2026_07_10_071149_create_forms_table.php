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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('activity_type_id')->constrained()->cascadeOnDelete();
            $table->enum('phase', ['registration', 'reporting']);
            $table->string('semester');
            $table->dateTime('open_at')->nullable();
            $table->dateTime('close_at')->nullable();
            $table->enum('status', ['draft', 'active', 'closed', 'archived']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
