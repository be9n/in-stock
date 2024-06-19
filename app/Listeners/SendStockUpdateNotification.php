<?php

namespace App\Listeners;

use App\Events\NowInStock;
use App\Models\User;
use App\Notifications\ImportantStockUpdateNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendStockUpdateNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NowInStock $event): void
    {
        User::first()->notify(new ImportantStockUpdateNotification($event->stock));
    }
}
