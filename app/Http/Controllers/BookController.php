<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Storage;
use DB;
use App\Models\Book;
use App\Models\Category;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Book::withTrashed()->paginate(5);

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
                'image' => 'required|image|max:1024',
                'category_id' => 'required|numeric',
                'name' => 'required|string|max:30',
                'description' => 'required|string|max:300',
                'price' => 'required|decimal:0,2',
                'author' => 'required|string|max:30',
                'quantity' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $image = $request->file('image');
            $imageFilename = 'book-' . now()->timestamp . '.' . $image->getClientOriginalExtension();
            $imageFilepath = 'books/' . $imageFilename;
            Storage::disk('public')->put($imageFilepath, file_get_contents($image), 'public');

            Book::create([
                'category_id' => $request->category_id,
                'image' => $imageFilename,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'author' => $request->author,
                'quantity' => $request->quantity,
            ]);
            
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
            $categories = Category::withTrashed()->get();
            $book = Book::withTrashed()->find($id);

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
                'image' => 'sometimes|image|max:1024',
                'category_id' => 'required|numeric',
                'name' => 'required|string|max:30',
                'description' => 'required|string|max:300',
                'price' => 'required|decimal:0,2',
                'author' => 'required|string|max:30',
                'quantity' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $book = Book::withTrashed()->find($id);

            if ($book) {
                if ($request->has('image')) {
                    Storage::disk('public')->delete('books/' . $book->image);

                    $image = $request->file('image');
                    $imageFilename = 'book-' . now()->timestamp . '.' . $image->getClientOriginalExtension();
                    $imageFilepath = 'books/' . $imageFilename;
                    Storage::disk('public')->put($imageFilepath, file_get_contents($image), 'public');
                    
                    $book->image = $imageFilename;
                }

                $book->update([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'price' => $request->price,
                    'author' => $request->author,
                    'quantity' => $request->quantity,
                ]);

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

    public function restore($id)
    {
        try {
            $book = Book::withTrashed()->find($id);

            if ($book) {
                $book->restore();

                return redirect()->back()->with('success', 'Book restored successfully.');
            }

            return redirect()->back()->with('error', 'Book not found.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    // user

    public function books(Request $request)
    {
        $books = Book::latest('id')
            ->when($q = $request->q, function ($q1, $q) {
                return $q1->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($q) . '%')
                    ->orWhereHas('category', function ($q2) use ($q) {
                        $q2->where(DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($q) . '%');
                    });
            })
            ->paginate(3)
            ->withQueryString();

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

        $books = Book::whereIn('id', $bookIds)->latest('id')->get();

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
                    $book = Book::find($request->id);

                    if ($book->quantity >= ($cart[$request->id] + 1)) {
                        $cart[$request->id] += 1;
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Not available in stock.'
                        ], 200);
                    }
                } else {
                    if ($cart[$request->id] > 1) {
                        $cart[$request->id] -= 1;
                    }
                }
                
                session(['cart' => $cart]);
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
