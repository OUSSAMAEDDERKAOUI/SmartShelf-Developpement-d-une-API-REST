<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\RangeController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/auth/register', [AuthController::class, 'register']);


Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/user/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::group(['middleware' => ['auth:sanctum','restrictRole:admin']], function(){
Route::get('/users', [AuthController::class, 'index']);
Route::put('/users/{user}', [AuthController::class, 'update']);
Route::get('/users/{user}', [AuthController::class, 'show']);
Route::delete('/users/{user}/delete', [AuthController::class, 'destroy']);

Route::apiResource('categories', CategoryController::class);
Route::apiResource('ranges', RangeController::class);
Route::apiResource('products', ProductController::class);
Route::get('ranges/{rangeId}/products', [RangeController::class, 'getProductsInRange']);
});
Route::group(['middleware'=>['auth:sanctum','restrictRole:client']],function(){
Route::get('ranges/{rangeId}/products', [RangeController::class, 'getProductsInRange']);
Route::get('range/products/promotion',[ProductController::class,'promotion']);
Route::get('range/products/popular',[ProductController::class,'popular']);
Route::post('/products/sale',[OrderController::class,'storeSale']);
Route::get('/product/search', [ProductController::class, 'search']);

});






