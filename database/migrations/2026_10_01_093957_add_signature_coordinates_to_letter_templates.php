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
        Schema::table('letter_templates', function (Blueprint $table) {
            $table->integer('sig_x')->nullable();
            $table->integer('sig_y')->nullable();
            $table->integer('sig_page')->default(1);
            $table->integer('sig_width')->default(150);
            $table->integer('sig_height')->default(75);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_templates', function (Blueprint $table) {
            $table->dropColumn(['sig_x', 'sig_y', 'sig_page', 'sig_width', 'sig_height']);
        });
    }
};
