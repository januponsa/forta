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
        Schema::table('academic_calendars', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('end_date');
            $table->string('publication_status')->default('draft')->after('is_active');
            $table->text('notes')->nullable()->after('publication_status');
        });

        Schema::table('academic_calendar_events', function (Blueprint $table) {
            $table->string('publication_status')->default('draft')->after('is_public');
            $table->string('external_url')->nullable()->after('publication_status');
            $table->text('internal_notes')->nullable()->after('external_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_calendar_events', function (Blueprint $table) {
            $table->dropColumn(['publication_status', 'external_url', 'internal_notes']);
        });

        Schema::table('academic_calendars', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'publication_status', 'notes']);
        });
    }
};
