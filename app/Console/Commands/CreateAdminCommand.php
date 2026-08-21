<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forta:admin-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new administrator account securely.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Create New Administrator Account');

        $name = $this->ask('Name');
        $username = $this->ask('Username');
        $email = $this->ask('Email (must be @' . config('auth.admin_domain') . ')');
        $role = $this->choice('Role', ['superadmin'], 0);
        
        $password = $this->secret('Password');
        $passwordConfirm = $this->secret('Confirm Password');

        $validator = Validator::make([
            'name' => $name,
            'username' => strtolower(trim($username)),
            'email' => strtolower(trim($email)),
            'role' => $role,
            'password' => $password,
            'password_confirmation' => $passwordConfirm,
        ], [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => ['required', 'email', 'unique:users,email', function ($attribute, $value, $fail) {
                if (!isAdminEmail($value)) {
                    $fail('The '.$attribute.' must be a valid @' . config('auth.admin_domain') . ' address.');
                }
            }],
            'role' => 'required|in:superadmin',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->line('- ' . $error);
            }
            return Command::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'username' => strtolower(trim($username)),
            'email' => strtolower(trim($email)),
            'normalized_email' => strtolower(trim($email)),
            'password' => Hash::make($password),
            'role' => $role,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $this->info("Administrator '{$user->username}' created successfully.");
        return Command::SUCCESS;
    }
}
