<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use  App\Observers\ChartOfAccountObserver;
use App\Models\ChartOfAccount;
use App\Models\Jurnalharian;


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
        //
        // ChartOfAccount::observe(\App\Observers\ChartOfAccountObserver::class);
        
    }
}


