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
            $table->unsignedBigInteger('logo_asset_id')->nullable()->after('type');
            $table->unsignedBigInteger('lecturer_id')->nullable()->after('signatory_position');

            $table->foreign('logo_asset_id')->references('id')->on('document_assets')->nullOnDelete();
            $table->foreign('lecturer_id')->references('id')->on('lecturers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_templates', function (Blueprint $table) {
            $table->dropForeign(['logo_asset_id']);
            $table->dropForeign(['lecturer_id']);
            $table->dropColumn(['logo_asset_id', 'lecturer_id']);
        });
    }
};
