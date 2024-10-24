<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Book;
use App\Models\Order;

class OrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->category = Category::create([
            'name' => 'Test Category'
        ]);
        
        $this->books = Book::factory()->count(4)->create([ 'category_id' => $this->category->id ]);

        $this->cart = array();
        
        foreach ($this->books as $book) {
            $this->cart[$book->id] = rand(1, 5);
        }

        $this->order = [
            'user_id' => $this->user->id,
            'name' => 'Test Name',
            'email' => 'test@gmail.com',
            'address' => 'Test Address'
        ];
    }

    public function test_order_screen_can_be_rendered(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get("/order");

        $response->assertOk();
    }

    public function test_order(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->withSession([ 'cart' => $this->cart ])
            ->post("/order", $this->order);

        $response->assertRedirect('/order');
        $this->assertDatabaseHas('orders', $this->order);

        $order = Order::where('user_id', $this->user->id)->latest()->first();
        
        foreach ($this->cart as $id => $quantity) {
            $this->assertDatabaseHas('order_books', [
                'order_id' => $order->id,
                'book_id' => $id,
                'quantity' => $quantity
            ]);
        }
    }
}
