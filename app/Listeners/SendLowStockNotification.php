<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Mail\LowStockMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendLowStockNotification implements ShouldQueue
{
    public function handle(LowStockDetected $event): void
    {
        $stock = $event->stock->load(['warehouse', 'inventoryItem']);
        $adminEmail = config('mail.from.address');

        if ($adminEmail) {
            Mail::to($adminEmail)->send(new LowStockMail($stock));
        }
    }
}
