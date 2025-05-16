<?php

namespace App\Providers;

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
        //
        
    }
}

class JurnalHarianObserver
{
    public function created(JurnalHarian $jurnal)
    {
        $jurnal->updateCoaBalance();
    }

    public function updated(JurnalHarian $jurnal)
    {
        $jurnal->updateCoaBalance();
    }

    public function deleted(JurnalHarian $jurnal)
    {
        $jurnal->reverseCoaBalance();
    }

    public function restored(JurnalHarian $jurnal)
    {
        $jurnal->updateCoaBalance();
    }
}
