<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\UpdateProductStock;
use App\Models\Product;

use Illuminate\Contracts\Support\ValidatedData;

class OrderController extends Controller
{
public function storeSale(Request $request){
    

$validateData=$request->validate([
'quantity'=>'required|integer',
'price'=>'required|numeric',
'product_id'=>'required|exists:products,id',
]);

$product=Product::find($validateData['product_id']);
 if($product->stock<$validateData['quantity']){

return response()->json([
    "error"=>'la quantité en stock est inférieure à la quantité demandée'
]);
 }


$total_price=$request->quantity*$request->price;

$sale=Order::create([
    'quantity'=>$validateData['quantity'],
    'total_price'=>$total_price,
'price'=>$validateData['price'],
'product_id'=>$validateData['product_id'],
]);

UpdateProductStock::dispatch($product->id, $validateData['quantity']);

return response()->json([
    'message' => 'Commande enregistrée avec succès.',
    'order'=>$sale,
]);



}


}
