<?php

namespace App\Observers;

use App\Models\ChartOfAccount;

class ChartOfAccountObserver
{
    /**
     * Handle the ChartOfAccount "created" event.
     */
    public function created(ChartOfAccount $chartOfAccount): void
    {
        //
        $chartOfAccount->updateDebitCreditBalances();
    }

    /**
     * Handle the ChartOfAccount "updated" event.
     */
    public function updated(ChartOfAccount $chartOfAccount): void
    {
        //
        $chartOfAccount->updateDebitCreditBalances();
    }

    /**
     * Handle the ChartOfAccount "deleted" event.
     */
    public function deleted(ChartOfAccount $chartOfAccount): void
    {
        //
    }

    /**
     * Handle the ChartOfAccount "restored" event.
     */
    public function restored(ChartOfAccount $chartOfAccount): void
    {
        //
    }

    /**
     * Handle the ChartOfAccount "force deleted" event.
     */
    public function forceDeleted(ChartOfAccount $chartOfAccount): void
    {
        //
    }
}
