<?php

namespace App\Providers;

use App\Models\Exam;
use App\Models\User;
use App\Policies\ExamPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Css;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Gate::policy(User::class , UserPolicy::class);
        Gate::policy(Exam::class , ExamPolicy::class);
        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true: null;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/custom.css'),

        ]);
    }
}
