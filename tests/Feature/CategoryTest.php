<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    
    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_category_screen_can_be_rendered(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get("/category");

        $response->assertOk();
    }

    public function test_store_category(): void
    {
        $category = 'Test Category';

        $response = $this
            ->actingAs($this->admin)
            ->post("/category", [ 'category' => $category ]);
        
        $response->assertRedirect('/category');
        
        $this->assertDatabaseHas('categories', [
            'name' => $category,
        ]);
    }

    public function test_update_category(): void
    {
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
    }

    public function test_delete_category(): void
    {
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
    }

    public function test_restore_category(): void
    {
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
    }
}
