<?php

namespace App\Providers;

use App\Models\Scan;
use App\Observers\ScanObserver;
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
    public function boot()
    {
        // Register the Scan observer
        Scan::observe(ScanObserver::class);
    }
}
