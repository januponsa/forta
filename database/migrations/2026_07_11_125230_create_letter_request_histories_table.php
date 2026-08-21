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
        Schema::create('letter_request_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_letter_request_id')->constrained('internship_letter_requests')->onDelete('cascade');
            
            // Actor can be student or user (admin)
            $table->string('actor_type'); // 'student' or 'user'
            $table->unsignedBigInteger('actor_id');
            
            $table->string('action'); // e.g., 'submitted', 'revised', 'approved', 'rejected', 'generated'
            $table->string('previous_status')->nullable();
            $table->string('new_status')->nullable();
            
            $table->text('note')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_request_histories');
    }
};
