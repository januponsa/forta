<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('students', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('students', 'remember_token')) {
                $table->rememberToken();
            }
        });

        // Backfill default username and password for existing students
        $defaultHash = Hash::make('password');
        $students = DB::table('students')->get();

        foreach ($students as $student) {
            $updates = [];

            if (empty($student->username)) {
                $cleanEmail = preg_replace('/\.archived(\.\d+)?$/', '', $student->email ?? '');
                $baseUsername = $cleanEmail && str_contains($cleanEmail, '@')
                    ? strtolower(trim(explode('@', $cleanEmail)[0]))
                    : strtolower(trim($student->nim));

                $candidate = $baseUsername ?: strtolower(trim($student->nim));
                $counter = 1;
                while (DB::table('students')->where('username', $candidate)->where('id', '!=', $student->id)->exists()) {
                    $candidate = $baseUsername . $counter++;
                }
                $updates['username'] = $candidate;
            }

            if (empty($student->password)) {
                $updates['password'] = $defaultHash;
            }

            if (!empty($updates)) {
                DB::table('students')->where('id', $student->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $cols = [];
            foreach (['username', 'password'] as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
