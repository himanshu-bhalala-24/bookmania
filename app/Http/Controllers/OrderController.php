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
        $orders = Order::latest('id')->get();

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

            $order = new Order();
            $order->user_id = auth()->id();
            $order->name = $request->name;
            $order->email = $request->email;
            $order->address = $request->address;
            $order->save();

            $cart = session('cart');

            if ($cart && !empty($cart)) {
                foreach ($cart as $id => $quantity) {
                    $orderBook = new OrderBook();
                    $orderBook->order_id = $order->id;
                    $orderBook->book_id = $id;
                    $orderBook->quantity = $quantity;
                    $orderBook->price = Book::find($id)->price;
                    $orderBook->save();
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
