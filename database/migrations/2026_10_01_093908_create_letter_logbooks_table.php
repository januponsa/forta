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
        Schema::create('letter_logbooks', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number')->index();
            $table->string('letter_title');
            $table->string('recipient_name')->nullable();
            $table->date('letter_date')->index(); // Official date on the letter
            $table->boolean('is_backdated')->default(false);
            $table->string('source_type')->index(); // polymorphic type
            $table->unsignedBigInteger('source_id')->index(); // polymorphic id
            $table->string('final_pdf_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_logbooks');
    }
};
