<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\Range;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockAlertMail;

it('can get all products', function () {
    // Créer des produits
    // Product::factory(3)->create();

    $response = $this->getJson('/api/products');

    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

it('can create a product', function () {
    $category = Category::factory()->create();
    $range = Range::factory()->create();

    $data = [
        'name' => 'Test Product',
        'description' => 'Product description',
        'price' => 100.0,
        'category_id' => $category->id,
        'stock' => 10,
        'range_id' => $range->id,
        'is_popular' => 'true',
        'is_on_promotion' => 'true',
    ];

    $response = $this->postJson('/api/products', $data);

    $response->assertStatus(201);
    $this->assertDatabaseHas('products', ['name' => 'Test Product']);
});

it('can show a product', function () {
    $product = Product::factory()->create();

    $response = $this->getJson("/api/products/{$product->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'id' => $product->id,
        'name' => $product->name,
    ]);
});

it('can update a product', function () {
    $product = Product::factory()->create();

    $data = [
        'name' => 'Updated Product',
        'price' => 150.0,
    ];

    $response = $this->putJson("/api/products/{$product->id}", $data);

    $response->assertStatus(200);
    $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
});

it('can delete a product', function () {
    $product = Product::factory()->create();

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('can search products by name', function () {
    $product = Product::factory()->create(['name' => 'Test Product']);

    $response = $this->getJson('/api/products/search?name=Test');

    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => 'Test Product']);
});

it('can get popular products', function () {
    $product = Product::factory()->create(['is_popular' => true]);

    $response = $this->getJson('/api/products/popular');

    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => $product->name]);
});

it('can get promotion products', function () {
    $product = Product::factory()->create(['is_on_promotion' => true]);

    $response = $this->getJson('/api/products/promotion');

    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => $product->name]);
});

it('can check low stock products', function () {

    $product = Product::factory()->create(['stock' => 5, 'min_stock' => 10]);

    Mail::fake();

    $response = $this->getJson('/api/products/checkLowStock');

    $response->assertStatus(200);
    Mail::assertSent(StockAlertMail::class);
});
