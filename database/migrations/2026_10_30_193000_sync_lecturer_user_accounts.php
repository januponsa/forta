<?php

use Illuminate\Database\Migrations\Migration;
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
        if (!Schema::hasTable('lecturers') || !Schema::hasTable('users')) {
            return;
        }

        // Sync existing lecturers with users table so every lecturer has a linked user_id and role
        $lecturers = DB::table('lecturers')->get();

        foreach ($lecturers as $lecturer) {
            $normalizedEmail = $lecturer->email ? strtolower(trim($lecturer->email)) : null;
            $user = null;

            if ($lecturer->user_id) {
                $user = DB::table('users')->where('id', $lecturer->user_id)->first();
            }

            if (!$user && $normalizedEmail) {
                $user = DB::table('users')
                    ->where('normalized_email', $normalizedEmail)
                    ->orWhere('email', $normalizedEmail)
                    ->first();
            }

            // Determine role: keep superadmin/admin_forta for structural admins (e.g. januponsa.dio), set 'dosen' for lecturers
            $isAdminStructural = $normalizedEmail && in_array($normalizedEmail, [
                'admin@pradita.ac.id',
                'admin@fortapradita.ac.id',
                'januponsa.dio@pradita.ac.id',
            ]);

            if ($user) {
                $newRole = $isAdminStructural ? $user->role : 'dosen';
                DB::table('users')->where('id', $user->id)->update([
                    'normalized_email' => $normalizedEmail ?: $user->normalized_email,
                    'role' => $newRole,
                    'updated_at' => now(),
                ]);

                if (!$lecturer->user_id) {
                    DB::table('lecturers')->where('id', $lecturer->id)->update([
                        'user_id' => $user->id,
                        'updated_at' => now(),
                    ]);
                }
            } elseif ($normalizedEmail || $lecturer->nip) {
                $email = $normalizedEmail ?: (strtolower(trim($lecturer->nip)) . '@pradita.ac.id');
                $baseUsername = $lecturer->nip ? strtolower(trim($lecturer->nip)) : explode('@', $email)[0];
                $username = $baseUsername;
                $counter = 1;
                while (DB::table('users')->where('username', $username)->exists()) {
                    $username = $baseUsername . $counter++;
                }

                $userId = DB::table('users')->insertGetId([
                    'name' => $lecturer->name,
                    'username' => $username,
                    'email' => $email,
                    'normalized_email' => $email,
                    'password' => Hash::make('password'),
                    'role' => $isAdminStructural ? 'admin_forta' : 'dosen',
                    'is_active' => (bool) $lecturer->is_active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('lecturers')->where('id', $lecturer->id)->update([
                    'user_id' => $userId,
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-destructive sync
    }
};
