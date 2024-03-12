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

    // Effectuer une requête GET à l'endpoint API pour récupérer tous les produits
    $response = $this->getJson('/api/products');

    // Vérifier que la réponse est OK (code 200) et contient 3 produits
    $response->assertStatus(200);
    $response->assertJsonCount(3);
});

it('can create a product', function () {
    // Créer une catégorie et une gamme nécessaires à la création du produit
    $category = Category::factory()->create();
    $range = Range::factory()->create();

    // Données pour le produit
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

    // Effectuer une requête POST pour créer un produit
    $response = $this->postJson('/api/products', $data);

    // Vérifier la réponse et la base de données
    $response->assertStatus(201);
    $this->assertDatabaseHas('products', ['name' => 'Test Product']);
});

it('can show a product', function () {
    // Créer un produit
    $product = Product::factory()->create();

    // Effectuer une requête GET pour afficher un produit
    $response = $this->getJson("/api/products/{$product->id}");

    // Vérifier la réponse
    $response->assertStatus(200);
    $response->assertJson([
        'id' => $product->id,
        'name' => $product->name,
    ]);
});

it('can update a product', function () {
    // Créer un produit
    $product = Product::factory()->create();

    // Données de mise à jour du produit
    $data = [
        'name' => 'Updated Product',
        'price' => 150.0,
    ];

    // Effectuer une requête PUT pour mettre à jour le produit
    $response = $this->putJson("/api/products/{$product->id}", $data);

    // Vérifier la réponse
    $response->assertStatus(200);
    $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
});

it('can delete a product', function () {
    // Créer un produit
    $product = Product::factory()->create();

    // Effectuer une requête DELETE pour supprimer un produit
    $response = $this->deleteJson("/api/products/{$product->id}");

    // Vérifier la réponse
    $response->assertStatus(200);
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('can search products by name', function () {
    // Créer un produit
    $product = Product::factory()->create(['name' => 'Test Product']);

    // Effectuer une requête GET avec un paramètre de recherche par nom
    $response = $this->getJson('/api/products/search?name=Test');

    // Vérifier que le produit est dans la réponse
    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => 'Test Product']);
});

it('can get popular products', function () {
    // Créer un produit populaire
    $product = Product::factory()->create(['is_popular' => true]);

    // Effectuer une requête GET pour récupérer les produits populaires
    $response = $this->getJson('/api/products/popular');

    // Vérifier que la réponse contient le produit populaire
    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => $product->name]);
});

it('can get promotion products', function () {
    // Créer un produit en promotion
    $product = Product::factory()->create(['is_on_promotion' => true]);

    // Effectuer une requête GET pour récupérer les produits en promotion
    $response = $this->getJson('/api/products/promotion');

    // Vérifier que la réponse contient le produit en promotion
    $response->assertStatus(200);
    $response->assertJsonFragment(['name' => $product->name]);
});

it('can check low stock products', function () {
    // Créer un produit avec un stock inférieur ou égal au stock minimum
    $product = Product::factory()->create(['stock' => 5, 'min_stock' => 10]);

    // Simuler l'envoi de l'alerte par e-mail
    Mail::fake();

    // Effectuer la requête pour vérifier le stock faible
    $response = $this->getJson('/api/products/checkLowStock');

    // Vérifier la réponse et si l'alerte par e-mail a été envoyée
    $response->assertStatus(200);
    Mail::assertSent(StockAlertMail::class);
});
