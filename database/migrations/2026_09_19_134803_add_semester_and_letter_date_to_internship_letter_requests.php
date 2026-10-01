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
        Schema::table('internship_letter_requests', function (Blueprint $table) {
            $table->string('semester')->nullable()->after('student_id');
            $table->date('letter_date')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internship_letter_requests', function (Blueprint $table) {
            $table->dropColumn('semester');
            $table->dropColumn('letter_date');
        });
    }
};
