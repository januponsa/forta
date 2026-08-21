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
        Schema::create('signature_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('title');
            $table->string('letter_type')->nullable();
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('signatory_id')->nullable()->constrained('signatories')->onDelete('set null');
            
            $table->string('original_file_path');
            $table->string('original_filename');
            $table->string('signed_file_path')->nullable();
            
            $table->integer('page_number')->nullable();
            $table->float('x_pos')->nullable();
            $table->float('y_pos')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('page_width')->nullable();
            $table->float('page_height')->nullable();
            $table->float('rotation')->nullable();
            
            $table->string('status')->default('draft');
            
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('revision_note')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->string('original_checksum')->nullable();
            $table->string('signed_checksum')->nullable();
            $table->string('email_error')->nullable();
            
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_requests');
    }
};
