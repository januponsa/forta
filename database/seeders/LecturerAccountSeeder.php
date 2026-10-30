<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Hash;

class LecturerAccountSeeder extends Seeder
{
    public function run(): void
    {
        $lecturers = [
            [
                'nip' => '316078403',
                'name' => 'Alfa Ryano Yohannis, S.T., M.T., Ph.D.',
                'email' => 'alfa.ryano@pradita.ac.id',
                'username' => 'alfa.ryano',
                'role' => 'dosen',
            ],
            [
                'nip' => '313046703',
                'name' => 'Arya Sanjaya, S.T., M.Kom.',
                'email' => 'arya.sanjaya@pradita.ac.id',
                'username' => 'arya.sanjaya',
                'role' => 'dosen',
            ],
            [
                'nip' => '302059002',
                'name' => 'Erick Dazki, S.Kom., M.Kom.',
                'email' => 'erick.dazki@pradita.ac.id',
                'username' => 'erick.dazki',
                'role' => 'dosen',
            ],
            [
                'nip' => '031400010',
                'name' => 'Januponsa Dio Firizqi, S.Kom., M.Kom.',
                'email' => 'januponsa.dio@pradita.ac.id',
                'username' => 'sekprodi.if',
                'role' => 'admin_forta',
            ],
            [
                'nip' => '323077202',
                'name' => 'Master Edison Siregar, S.T., M.Kom.',
                'email' => 'master.edison@pradita.ac.id',
                'username' => 'master.edison',
                'role' => 'dosen',
            ],
            [
                'nip' => '309059801',
                'name' => 'Refgiufi Patria Avrianto, S.Kom., M.Kom.',
                'email' => 'refgiufi.patria@pradita.ac.id',
                'username' => 'refgiufi.patria',
                'role' => 'dosen',
            ],
        ];

        foreach ($lecturers as $item) {
            $normalizedEmail = strtolower(trim($item['email']));

            // Check if user exists by email or username
            $user = User::where('email', $normalizedEmail)
                        ->orWhere('normalized_email', $normalizedEmail)
                        ->orWhere('username', $item['username'])
                        ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $item['name'],
                    'username' => $item['username'],
                    'email' => $normalizedEmail,
                    'normalized_email' => $normalizedEmail,
                    'password' => Hash::make('password'),
                    'role' => $item['role'],
                    'is_active' => true,
                    'status_akun' => 'Login Diizinkan',
                ]);
                $this->command?->info("Created user for {$item['name']} ({$item['role']})");
            } else {
                $user->update([
                    'name' => $item['name'],
                    'email' => $normalizedEmail,
                    'normalized_email' => $normalizedEmail,
                    'role' => $item['role'],
                    'is_active' => true,
                    'password' => Hash::make('password'),
                    'status_akun' => 'Login Diizinkan',
                ]);
                $this->command?->info("Updated user for {$item['name']} ({$item['role']})");
            }

            // Sync with Lecturer table
            $lecturer = Lecturer::where('nip', $item['nip'])
                                ->orWhere('email', $normalizedEmail)
                                ->orWhere('name', 'like', '%' . explode(',', $item['name'])[0] . '%')
                                ->first();

            if ($lecturer) {
                $lecturer->update([
                    'user_id' => $user->id,
                    'nip' => $item['nip'],
                    'email' => $normalizedEmail,
                    'is_active' => true,
                ]);
                $this->command?->info("Linked lecturer {$lecturer->name} with User ID {$user->id}");
            } else {
                Lecturer::create([
                    'user_id' => $user->id,
                    'nip' => $item['nip'],
                    'name' => $item['name'],
                    'email' => $normalizedEmail,
                    'is_active' => true,
                ]);
                $this->command?->info("Created & linked lecturer record for {$item['name']}");
            }
        }
    }
}
