<?php

namespace App\Providers;

use App\Models\Activity;
use App\Policies\ActivityPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Auth::provider('plaintext', function ($app, array $config) {
            return new PlainTextUserProvider();
        });

        Gate::policy(Activity::class, ActivityPolicy::class);

        // El superadmin (admin de la plataforma) tiene acceso total.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });

        // Configuracion de acceso por areas (pantalla admin): solo superadmin.
        Gate::define('manage-areas', function ($user) {
            return $user->hasRole('superadmin');
        });

        // Reportes: superadmin (via before) o responsable de area.
        Gate::define('view-reports', function ($user) {
            return $user->isResponsible();
        });
    }
}
