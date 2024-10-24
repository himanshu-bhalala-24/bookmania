<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Book;

class BookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->category = Category::create([
            'name' => 'Test Category'
        ]);

        $this->bookData = [
            'category_id' => $this->category->id,
            'image' => UploadedFile::fake()->image('default.jpg'),
            'name' => 'Test Book',
            'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
            'price' => 850,
            'author' => 'Test Author',
            'quantity' => 50,
        ];

        $this->book = Book::create($this->bookData);
        $this->books = Book::factory()->count(2)->create([ 'category_id' => $this->category->id ]);
    }

    public function test_book_screen_can_be_rendered(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get("/book");

        $response->assertOk();
    }

    public function test_store_book(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post("/book", $this->bookData);
        
        $response->assertRedirect('/book');
        
        $this->assertDatabaseHas('books', $this->bookData);

        $book = Book::where('name', $this->bookData['name'])->first();
    }

    public function test_update_book(): void
    {
        $editedBook = [
            'category_id' => $this->category->id,
            'image' => UploadedFile::fake()->image('default-edited.jpg'),
            'name' => 'Test Book Edited',
            'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s",
            'price' => 850,
            'author' => 'Test Author Edited',
            'quantity' => 50,
        ];

        $response = $this
            ->actingAs($this->admin)
            ->put("/book/{$this->book->id}", $editedBook);
        
        $response->assertRedirect('/book');
        
        unset($editedBook['image']);
        $this->assertDatabaseHas('books', $editedBook);
    }

    public function test_delete_book(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->delete("/book/{$this->book->id}");
        
        $response->assertRedirect();
        
        $this->assertSoftDeleted($this->book);
    }

    public function test_restore_book(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->put("/book/{$this->book->id}/restore");
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('books', [
            'id' => $this->book->id,
            'deleted_at' => NULL
        ]);
    }

    // 
    public function test_books_screen_can_be_rendered(): void
    {
        $cart = array( $this->books->first()->id => 2 );
        
        $response = $this
            ->actingAs($this->user)
            ->withSession([ 'cart' => $cart ])
            ->get("/books");

        $response->assertOk();

        $response->assertViewHas('books', function ($viewBooks) {
            foreach ($this->books as $book) {
                if (!$viewBooks->contains($book)) {
                    return false;
                }
            }
            return true;
        });
        $response->assertViewHas('cart', $cart);
    }

    public function test_cart_screen_can_be_rendered(): void
    {
        $cart = array( $this->books->first()->id => 2 );
        
        $response = $this
            ->actingAs($this->user)
            ->withSession([ 'cart' => $cart ])
            ->get("/cart");

        $response->assertOk();

        $response->assertViewHas('books', function ($viewBooks) use ($cart) {
            foreach ($this->books as $book) {
                return array_key_exists($book->id, $cart);
            }
        });
        $response->assertViewHas('cart', $cart);
    }

    public function test_empty_cart(): void
    {
        $cart = array( $this->books->first()->id => 2 );
        
        $response = $this
            ->actingAs($this->user)
            ->withSession([ 'cart' => $cart ])
            ->get("/empty-cart");

        $response->assertRedirect('/books');
        $this->assertNull(session('cart'));
    }

    public function test_add_to_cart(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->postJson('/add-to-cart', [ 'id' => $this->book->id ]);
            
        $response
            ->assertOk()
            ->assertExactJson([
                'success' => true
            ])
            ->assertSessionHas('cart', function ($cart) {
                return array_key_exists($this->book->id, $cart);
            });
    }

    public function test_remove_from_cart(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->postJson('/remove-from-cart', [ 'id' => $this->book->id ]);
            
        $response
            ->assertOk()
            ->assertExactJson([
                'success' => true
            ])
            ->assertSessionHas('cart', function ($cart) {
                if ($cart) {
                    return !array_key_exists($this->book->id, $cart);
                }
                return !$cart;
            });
    }

    public function test_change_book_qunatity(): void
    {
        $cart = array( $this->book->id => 2 );

        $response = $this
            ->actingAs($this->user)
            ->withSession([ 'cart' => $cart ])
            ->postJson('/change-quantity', [
                'id' => $this->book->id,
                'isIncrease' => true
            ]);
        
        $response
            ->assertOk()
            ->assertExactJson([
                'success' => true
            ])
            ->assertSessionHas('cart', function ($cart) {
                if ($cart && isset($cart[$this->book->id])) {
                    return $cart[$this->book->id] == 3;
                }
                return true;
            });
    }
}
