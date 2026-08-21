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
        Schema::create('letter_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('internship'); // Type of letter (for future extensibility)
            $table->integer('year');
            $table->integer('month')->nullable(); // If resetting monthly, otherwise null
            $table->integer('last_number')->default(0);
            $table->string('format')->default('{nomor_urut}/{kode_surat}/{bulan_romawi}/{tahun}');
            $table->timestamps();

            $table->unique(['type', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_number_sequences');
    }
};
