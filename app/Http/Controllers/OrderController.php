<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Book;
use App\Models\Order;
use App\Models\OrderBook;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = auth()->user()->orders()->latest('id')->get();

        return view('order.index', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:30',
                'email' => 'required|email',
                'address' => 'required|string|max:100'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $order = Order::create([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
            ]);

            $cart = session('cart');

            if ($cart && !empty($cart)) {
                $uBooks = array();
                
                foreach ($cart as $id => $quantity) {
                    $book = Book::find($id);

                    if ($book->quantity < $quantity) {
                        $uBooks[$book->name] = $book->quantity;
                    }
                }

                if (count($uBooks)) {
                    $message = '';

                    foreach ($uBooks as $book => $quantity) {
                        $message .= 'Available quantity for ' . $book . ' is ' . $quantity . '<br>';
                    }

                    return redirect()->back()->with('error', $message);
                }

                foreach ($cart as $id => $quantity) {
                    $book = Book::find($id);
                
                    $order->orderBooks()->create([
                        'book_id' => $id,
                        'quantity' => $quantity,
                        'price' => $book->price
                    ]);

                    $book->decrement('quantity', $quantity);
                    $book->save();
                }
            }

            session()->forget('cart');
            
            return redirect()->route('order.index')->with('success', 'Order placed successfully.');
        } catch (\Throwable $th) {
            info($th);
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }
}
