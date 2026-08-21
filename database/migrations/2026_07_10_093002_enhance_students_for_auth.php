<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'normalized_email')) {
                $table->string('normalized_email')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('students', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }
            if (!Schema::hasColumn('students', 'academic_status')) {
                $table->string('academic_status')->default('inactive')->after('avatar');
            }
            if (!Schema::hasColumn('students', 'login_enabled')) {
                $table->boolean('login_enabled')->default(true)->after('academic_status');
            }
            if (!Schema::hasColumn('students', 'approval_status')) {
                $table->string('approval_status')->default('pending')->after('login_enabled');
            }
            if (!Schema::hasColumn('students', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('students', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            }
            if (!Schema::hasColumn('students', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('students', 'source_type')) {
                $table->string('source_type')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('students', 'source_batch')) {
                $table->string('source_batch')->nullable()->after('source_type');
            }
            if (!Schema::hasColumn('students', 'source_row')) {
                $table->string('source_row')->nullable()->after('source_batch');
            }
            if (!Schema::hasColumn('students', 'source_hash')) {
                $table->string('source_hash')->nullable()->after('source_row');
            }
            if (!Schema::hasColumn('students', 'manual_override')) {
                $table->boolean('manual_override')->default(false)->after('source_hash');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'normalized_email',
                'avatar',
                'academic_status',
                'login_enabled',
                'approval_status',
                'approved_at',
                'approved_by',
                'last_login_at',
                'source_type',
                'source_batch',
                'source_row',
                'source_hash',
                'manual_override'
            ]);
        });
    }
};
