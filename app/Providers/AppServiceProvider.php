<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        
        Carbon::setLocale('id');

        Gate::define('access-admin', function (User $user) {
            return $user->hasRole('admin');
        });
        
        Gate::define('access-student', function ($user) {
            return $user instanceof \App\Models\Student;
        });

        Gate::define('users.delete_permanently', function ($user) {
            if ($user->role === 'superadmin') {
                $position = $user->lecturer->position ?? null;
                return in_array($position, ['Kaprodi', 'Sekprodi']);
            }
            return false;
        });

        \App\Models\DocumentTemplate::observe(\App\Observers\DocumentTemplateObserver::class);
    }
}
