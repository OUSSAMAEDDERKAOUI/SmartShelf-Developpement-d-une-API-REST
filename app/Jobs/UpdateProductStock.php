<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable; 


class UpdateProductStock implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels,Dispatchable;


    public $productId;
    public $quantitySold;

    /**
     * Créer une nouvelle instance de job.
     *
     * @param int $productId
     * @param int $quantitySold
     */
    public function __construct($productId, $quantitySold)
    {
        $this->productId = $productId;
        $this->quantitySold = $quantitySold;
    }

    /**
     * Exécuter le job.
     *
     * @return void
     */
    public function handle()
    {
        $product = Product::find($this->productId);

        if ($product) {

            $product->stock -= $this->quantitySold;
            $product->save();
        }
    }
}
