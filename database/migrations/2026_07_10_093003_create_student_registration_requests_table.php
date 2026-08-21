<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('google_id')->nullable();
            $table->string('google_email');
            $table->string('normalized_email')->unique();
            $table->string('nim')->unique();
            $table->string('name');
            $table->string('angkatan');
            $table->string('google_avatar')->nullable();
            $table->string('status')->default('pending');
            $table->string('conflict_type')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->text('request_note')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registration_requests');
    }
};
