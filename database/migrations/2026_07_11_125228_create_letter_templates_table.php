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
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('internship')->unique();
            
            // Logo & Header
            $table->string('logo_path')->nullable();
            $table->text('kop_text')->nullable();
            $table->string('university_name')->nullable();
            $table->string('campus_address')->nullable();
            $table->string('contact_info')->nullable();
            
            // Letter Meta
            $table->string('city')->default('Tangerang');
            $table->string('letter_code')->default('P-IF/Pradita');
            $table->string('subject')->default('Surat Pengantar Kerja Praktik');
            
            // Content Paragraphs
            $table->text('opening_paragraph')->nullable();
            $table->text('purpose_paragraph')->nullable();
            $table->text('closing_paragraph')->nullable();
            
            // Signatory
            $table->string('signatory_name')->nullable();
            $table->string('signatory_position')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('stamp_path')->nullable();
            
            // Number Formatting
            $table->string('number_format')->default('{nomor_urut}/{kode_surat}/{bulan_romawi}/{tahun}');
            
            // Page Formatting
            $table->integer('margin_top')->default(30);
            $table->integer('margin_bottom')->default(30);
            $table->integer('margin_left')->default(30);
            $table->integer('margin_right')->default(30);
            $table->string('paper_size')->default('a4');
            $table->text('footer_text')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_templates');
    }
};
