<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Certifique-se de que a linha 'use Illuminate\Pagination\Paginator;' NÃO esteja aqui

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
        // Certifique-se de que a linha 'Paginator::useBootstrap4();' NÃO esteja aqui
    }
}
