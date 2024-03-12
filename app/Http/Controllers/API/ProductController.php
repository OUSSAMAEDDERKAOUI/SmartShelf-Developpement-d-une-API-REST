<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Range;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockAlertMail;
use PhpParser\Node\Stmt\Return_;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock'=>'required|numeric',
            'range_id' => 'required|exists:ranges,id',
            'is_popular'=>'nullable|string',
            'is_on_promotion'=>'nullable|string',
        ]);

        $product = Product::create([
            'name' => $validateData['name'],
            'description' => $validateData['description'],
            'price' => $validateData['price'],
            'category_id' => $validateData['category_id'],
            'stock'=>$validateData['stock'],
            'range_id'=>$validateData['range_id'],
            'is_popular'=>$validateData['is_popular'],
            'is_on_promotion'=>$validateData['is_on_promotion'],
        ]);

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validateData = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'stock'=>'nullable|numeric',
            'range_id' => 'nullable|exists:ranges,id',
            'is_popular'=>'nullable|string',
            'is_on_promotion'=>'nullable|string',
        ]);

        $product->update($validateData);

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }


   public function search(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
        ]);

        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $validatedData['name'] . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $validatedData['category_id']);
        }

        $products = $query->get();
        if(count($products)==0){
            return response()->json(["message"=>"Aucun résultat correspondant à cette recherche."]);
 
        }
            return response()->json($products);
    }


    public function popular()
    {
        $popularProducts = Product::with("range")
        ->where('is_popular', true)->get();
        if($popularProducts->isEmpty()){
            return response()->json(["message"=>"Produits sans rayon"]);

        }
        return response()->json([
            "products"=>$popularProducts,
        ]);
    }

    public function promotion()
    {
  
        $promotionProducts = Product::with("range")
        ->where('is_on_promotion', true)->get();
        if($promotionProducts->isEmpty()){
            return response()->json(["message"=>"Produits sans rayon"]);

        }
        return response()->json([
            "products"=>$promotionProducts,
        ]);
    }


    public function checkLowStock()
    {
        $products = Product::whereColumn('stock', '<=', 'min_stock')->get();

        if ($products->isNotEmpty()) {
            $this->sendStockAlert($products);
        }

        return response()->json([
            'message' => 'Vérification des stocks effectuée.'
        ]);
    }

    protected function sendStockAlert($products)
    {
        Mail::to('latidob554@cybtric.com')->send(new StockAlertMail($products));
    }


}
