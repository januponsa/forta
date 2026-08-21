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
        Schema::create('internship_letter_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            // Company Details
            $table->string('company_name');
            $table->string('recipient_name')->default('Bapak/Ibu HRD');
            $table->text('company_address');
            $table->string('company_city');
            $table->string('placement_location')->nullable();
            
            // Internship Details
            $table->string('internship_position')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('duration_notes')->nullable(); // For cases like "3 Months" instead of end date
            $table->text('purpose')->nullable();
            $table->text('additional_notes')->nullable();
            
            // Files
            $table->string('attachment_path')->nullable();
            $table->string('final_pdf_path')->nullable();
            
            // Status & Approval
            $table->enum('status', [
                'draft', 
                'submitted', 
                'under_review', 
                'revision_required', 
                'approved', 
                'rejected', 
                'generated', 
                'completed'
            ])->default('draft');
            
            $table->string('letter_number')->nullable()->unique();
            $table->timestamp('generated_at')->nullable();
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->text('rejection_reason')->nullable();
            $table->text('revision_note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_letter_requests');
    }
};
