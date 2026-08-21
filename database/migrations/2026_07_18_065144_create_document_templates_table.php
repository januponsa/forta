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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Berita Acara Sidang Magang"
            $table->string('type')->unique(); // e.g. "defense_f1"
            $table->text('header_html')->nullable();
            $table->longText('body_html');
            $table->text('footer_html')->nullable();
            
            // Layout Settings
            $table->string('paper_size')->default('A4');
            $table->decimal('margin_top', 8, 2)->default(30);
            $table->decimal('margin_bottom', 8, 2)->default(30);
            $table->decimal('margin_left', 8, 2)->default(30);
            $table->decimal('margin_right', 8, 2)->default(30);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
