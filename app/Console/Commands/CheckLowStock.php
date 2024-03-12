<?php

// CheckLowStock.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\API\ProductController;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check';
    protected $description = 'Vérifier les produits à stock faible';

    public function handle()
    {
        $controller = new ProductController();
        $controller->checkLowStock();
    }
}
