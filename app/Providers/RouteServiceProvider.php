<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the admin "home" route.
     */
    public const HOME = '/admin/dashboard';

    /**
     * The path to the student "home" route.
     */
    public const STUDENT_HOME = '/student/dashboard';

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
        //
    }
}
