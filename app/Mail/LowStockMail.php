<?php

namespace App\Mail;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Stock $stock)
    {
    }

    public function build(): self
    {
        return $this->subject(__('api.low_stock_alert'))
            ->view('emails.low_stock')
            ->with([
                'stock' => $this->stock,
                'warehouse' => $this->stock->warehouse,
                'item' => $this->stock->inventoryItem,
            ]);
    }
}
