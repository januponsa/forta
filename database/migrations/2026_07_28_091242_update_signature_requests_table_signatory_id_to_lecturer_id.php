<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate data from signatories to lecturers
        $signatories = DB::table('signatories')->get();
        $mapping = []; // signatory_id => lecturer_id

        foreach ($signatories as $sig) {
            // Find existing lecturer by email or name
            $lecturer = DB::table('lecturers')
                ->where('email', $sig->email)
                ->orWhere('name', 'like', '%' . explode(',', $sig->name)[0] . '%')
                ->first();

            if ($lecturer) {
                // Update lecturer with signatory data
                DB::table('lecturers')->where('id', $lecturer->id)->update([
                    'position' => $sig->position,
                    'stamp_path' => $sig->stamp_path,
                    'default_width' => $sig->default_width,
                    'default_height' => $sig->default_height,
                    'include_name' => $sig->include_name,
                    'include_position' => $sig->include_position,
                    'include_date' => $sig->include_date,
                    'allowed_roles' => $sig->allowed_roles,
                ]);

                // Update signature path if it exists and lecturer doesn't have one
                if ($sig->signature_path && !$lecturer->signature_path) {
                    DB::table('lecturers')->where('id', $lecturer->id)->update([
                        'signature_path' => $sig->signature_path
                    ]);
                }

                $mapping[$sig->id] = $lecturer->id;
            } else {
                // Insert new lecturer
                $newLecturerId = DB::table('lecturers')->insertGetId([
                    'name' => $sig->name,
                    'email' => $sig->email,
                    'position' => $sig->position,
                    'is_active' => $sig->is_active,
                    'signature_path' => $sig->signature_path,
                    'stamp_path' => $sig->stamp_path,
                    'default_width' => $sig->default_width,
                    'default_height' => $sig->default_height,
                    'include_name' => $sig->include_name,
                    'include_position' => $sig->include_position,
                    'include_date' => $sig->include_date,
                    'allowed_roles' => $sig->allowed_roles,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $mapping[$sig->id] = $newLecturerId;
            }
        }

        // Add lecturer_id column to signature_requests
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('lecturer_id')->nullable()->after('signatory_id');
        });

        // Update signature_requests records
        foreach ($mapping as $sigId => $lecturerId) {
            DB::table('signature_requests')->where('signatory_id', $sigId)->update(['lecturer_id' => $lecturerId]);
        }

        // Drop signatory_id column
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropForeign(['signatory_id']);
            $table->dropColumn('signatory_id');
        });
    }

    public function down(): void
    {
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropColumn('lecturer_id');
        });
    }
};
