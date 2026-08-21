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
            $table->string('semester_code')->unique()->after('id')->nullable();
            $table->string('semester_type')->nullable()->after('semester_name'); // gasal, genap, antara
            $table->string('academic_year')->nullable()->after('semester_type');
            $table->date('display_start_date')->nullable()->after('academic_year');
            $table->date('display_end_date')->nullable()->after('display_start_date');
            $table->string('source_document_title')->nullable()->after('end_date');
            $table->string('source_letter_number')->nullable()->after('source_document_title');
            $table->date('source_letter_date')->nullable()->after('source_letter_number');
            $table->string('source_document_code')->nullable()->after('source_letter_date');
            $table->integer('source_page')->nullable()->after('source_document_code');
            $table->string('timezone')->nullable()->after('source_page');
        });

        Schema::table('academic_calendar_events', function (Blueprint $table) {
            $table->renameColumn('event_type', 'category_code');
        });

        Schema::table('academic_calendar_events', function (Blueprint $table) {
            $table->string('group_key')->nullable()->after('description');
            $table->boolean('is_tentative')->default(false)->after('group_key');
            $table->integer('source_page')->nullable()->after('is_tentative');
            $table->string('source_label')->nullable()->after('source_page');
            $table->string('source_type')->default('department')->after('source_label'); // official_university or department
            $table->string('source_reference')->nullable()->after('source_type');
            $table->boolean('is_source_locked')->default(false)->after('source_reference');
            
            $table->index('group_key');
            $table->index('category_code'); // re-index since rename might drop it depending on driver, or just to be safe
        });

        Schema::create('academic_calendar_meeting_weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_calendar_id')->constrained()->cascadeOnDelete();
            $table->integer('meeting_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('note')->nullable();
            $table->boolean('is_instructional')->default(true);
            $table->integer('source_page')->nullable();
            $table->timestamps();

            $table->unique(['academic_calendar_id', 'meeting_number'], 'unique_calendar_meeting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_calendar_meeting_weeks');

        Schema::table('academic_calendar_events', function (Blueprint $table) {
            $table->dropIndex(['group_key']);
            $table->dropColumn([
                'group_key',
                'is_tentative',
                'source_page',
                'source_label',
                'source_type',
                'source_reference',
                'is_source_locked'
            ]);
        });

        Schema::table('academic_calendar_events', function (Blueprint $table) {
            $table->renameColumn('category_code', 'event_type');
        });

        Schema::table('academic_calendars', function (Blueprint $table) {
            $table->dropColumn([
                'semester_code',
                'semester_type',
                'academic_year',
                'display_start_date',
                'display_end_date',
                'source_document_title',
                'source_letter_number',
                'source_letter_date',
                'source_document_code',
                'source_page',
                'timezone'
            ]);
        });
    }
};
