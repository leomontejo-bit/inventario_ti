<?php

namespace App\Providers;

use App\Services\ConfiguracionSistemaService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer(['layouts.app', 'auth.login'], function ($view): void {
            $view->with('configuracionSistema', app(ConfiguracionSistemaService::class)->obtener());
        });
    }
}
