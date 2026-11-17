<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registry metrik Prometheus (Pertemuan 11).
        // Di container memakai APCu supaya angka bertahan antar request;
        // di CI/test (APCu tidak tersedia) memakai InMemory supaya test tetap jalan.
        $this->app->singleton(CollectorRegistry::class, function () {
            $storage = (extension_loaded('apcu') && apcu_enabled())
                ? new APC()
                : new InMemory();

            return new CollectorRegistry($storage, false);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
