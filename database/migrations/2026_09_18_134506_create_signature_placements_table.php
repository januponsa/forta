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
        Schema::create('signature_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signature_request_id')->constrained('signature_requests')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained('lecturers')->nullOnDelete();
            $table->integer('page_number')->default(1);
            $table->double('x_pos')->nullable();
            $table->double('y_pos')->nullable();
            $table->double('width')->nullable();
            $table->double('height')->nullable();
            $table->boolean('include_stamp')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_placements');
    }
};
