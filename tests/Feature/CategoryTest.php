<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

test('category screen can be rendered', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get("/category");
        
    expect($response->status())->toBe(200);
});

test('store category', function () {
    $category = 'Test Category';

    $response = $this
        ->actingAs($this->admin)
        ->post("/category", [ 'category' => $category ]);
    
    $response->assertRedirect('/category');
    
    $this->assertDatabaseHas('categories', [
        'name' => $category,
    ]);
});

test('update category', function () {
    $category = Category::create([
        'name' => 'Test Category'
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->put("/category/{$category->id}", [ 'category' => 'Updated Category' ]);
    
    $response->assertRedirect('/category');
    
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Category'
    ]);
});

test('delete category', function () {
    $category = Category::create([
        'name' => 'Test Category'
    ]);

    $book = $category->books()->create([
        'image' => 'default.png',
        'name' => 'Test Book',
        'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
        'price' => 850,
        'author' => 'Test Author',
        'quantity' => 50,
    ]);
    
    $response = $this
        ->actingAs($this->admin)
        ->delete("/category/{$category->id}");
    
    $response->assertRedirect();
    
    $this->assertSoftDeleted($category);
    $this->assertSoftDeleted($book);
});

test('restore category', function () {
    $category = Category::create([
        'name' => 'Test Category',
        'deleted_at' => now()
    ]);

    $book = $category->books()->create([
        'image' => 'default.png',
        'name' => 'Test Book',
        'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
        'price' => 850,
        'author' => 'Test Author',
        'quantity' => 50,
        'deleted_at' => now()
    ]);
    
    $response = $this
        ->actingAs($this->admin)
        ->put("/category/{$category->id}/restore");
    
    $response->assertRedirect();
    
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'deleted_at' => NULL
    ]);

    $this->assertDatabaseHas('books', [
        'id' => $book->id,
        'deleted_at' => NULL
    ]);
});