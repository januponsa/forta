<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_blasts', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('body_html');
            $table->string('delivery_mode')->default('bcc'); // to, cc, bcc, individual
            $table->string('target_description')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->string('status')->default('draft'); // draft, scheduled, queued, sending, completed, partially_failed, failed, cancelled
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('email_blast_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_blast_id')->constrained('email_blasts')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            
            // Snapshot data
            $table->string('name')->nullable();
            $table->string('nim')->nullable();
            $table->string('email');
            $table->string('angkatan')->nullable();
            
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('email_blast_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_blast_id')->constrained('email_blasts')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->longText('body_html');
            $table->string('category')->nullable();
            $table->timestamps();
        });

        Schema::create('saved_audiences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('filter_criteria');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_audiences');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('email_blast_attachments');
        Schema::dropIfExists('email_blast_recipients');
        Schema::dropIfExists('email_blasts');
    }
};
