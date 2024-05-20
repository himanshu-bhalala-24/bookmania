<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Models\Category;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Book::paginate(10);

        return view('book.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get();

        return view('book.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required|numeric',
                'book' => 'required|string|max:30',
                'description' => 'required|string|max:300',
                'price' => 'required|numeric',
                'author' => 'required|string|max:30',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $book = new Book();
            $book->category_id = $request->category;
            $book->name = $request->book;
            $book->description = $request->description;
            $book->price = $request->price;
            $book->author = $request->author;
            $book->save();
            
            return redirect()->route('book.index')->with('success', 'Book created successfully.');
        } catch (\Throwable $th) {
            info($th);
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $categories = Category::get();
            $book = Book::find($id);

            if ($book) {
                return view('book.edit', compact('categories', 'book'));
            }

            return redirect()->back()->with('error', 'Book not found.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required|numeric',
                'book' => 'required|string|max:30',
                'description' => 'required|string|max:300',
                'price' => 'required|numeric',
                'author' => 'required|string|max:30',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $book = Book::find($id);

            if ($book) {
                $book->category_id = $request->category;
                $book->name = $request->book;
                $book->description = $request->description;
                $book->price = $request->price;
                $book->author = $request->author;
                $book->save();

                return redirect()->route('book.index')->with('success', 'Book updated successfully.');
            }

            return redirect()->back()->with('error', 'Book not found.');
        } catch (\Throwable $th) {
            info($th);
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $book = Book::find($id);

            if ($book) {
                $book->delete();

                return redirect()->back()->with('success', 'Book deleted successfully.');
            }

            return redirect()->back()->with('error', 'Book not found.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    // user

    public function books()
    {
        $books = Book::orderBy('id', 'DESC')->get();
        $cart = session('cart');

        return view('book.list', compact('books', 'cart'));
    }

    public function cart()
    {
        $cart = session('cart');
        $bookIds = array();

        if ($cart && !empty($cart)) {
            $bookIds = array_keys($cart);
        }

        $books = Book::whereIn('id', $bookIds)->orderBy('id', 'DESC')->get();

        return view('book.cart', compact('books', 'cart'));
    }

    public function emptyCart()
    {
        session()->forget('cart');

        return redirect()->route('books')->with('success', 'Your cart was emptied...');
    }

    public function addToCart(Request $request)
    {
        try {
            $cart = session('cart');
        
            if ($cart) {
                $cart[$request->id] = 1;
            } else {
                $cart = array( $request->id => 1 );
            }
        
            session(['cart' => $cart]);
            info(session('cart'));

            return response()->json([
                'success' => true
            ], 200);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    public function removeFromCart(Request $request)
    {
        try {
            $cart = session('cart');
        
            if ($cart) {
                unset($cart[$request->id]);
                session(['cart' => $cart]);
                info(session('cart'));
            }

            return response()->json([
                'success' => true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false
            ], 500);
        }
    }

    public function changeQuantity(Request $request)
    {
        try {
            $cart = session('cart');
            
            if ($cart) {
                if ($request->isIncrease) {
                    $cart[$request->id] += 1;
                } else {
                    if ($cart[$request->id] > 1) {
                        $cart[$request->id] -= 1;
                    }
                }
                
                session(['cart' => $cart]);
                info(session('cart'));
            }
            
            return response()->json([
                'success' => true
            ], 200);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'success' => false
            ], 500);
        }
    }
}
