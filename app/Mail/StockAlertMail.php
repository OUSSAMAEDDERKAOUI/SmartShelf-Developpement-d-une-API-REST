<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function build()
    {
        return $this->subject('Alerte de stock faible')
                    ->view('emails.stock_alert')
                    ->with('products', $this->products);
    }
}
