<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::before(function (User $user, string $ability) {
            return in_array($user->role, ['super_admin', 'admin'], true) ? true : null;
        });

        foreach (config('permissions.groups', []) as $group) {
            foreach (array_keys($group['permissions'] ?? []) as $ability) {
                Gate::define($ability, fn (User $user) => $user->hasPermission($ability));
            }
        }
    }
}
