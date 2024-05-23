<?php

namespace App\Observers;

use App\Models\Scan;
use Illuminate\Support\Facades\Log;

class ScanObserver
{
    /**
     * Handle the Scan "created" event.
     */
    public function created(Scan $scan): void
    {
        // Log the creation of a new Scan
        Log::info('New Scan created: ', ['scan' => $scan]);
    }

    /**
     * Handle the Scan "updated" event.
     */
    public function updated(Scan $scan): void
    {
        //
    }

    /**
     * Handle the Scan "deleted" event.
     */
    public function deleted(Scan $scan): void
    {
        //
    }

    /**
     * Handle the Scan "restored" event.
     */
    public function restored(Scan $scan): void
    {
        //
    }

    /**
     * Handle the Scan "force deleted" event.
     */
    public function forceDeleted(Scan $scan): void
    {
        //
    }
}
