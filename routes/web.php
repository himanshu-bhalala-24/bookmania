<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // admin routes
    Route::middleware(['role:admin'])->group(function () {
        // category
        Route::resource('/category', CategoryController::class);
    
        // book
        Route::resource('/book', BookController::class);
    });

    // user routes
    Route::middleware(['role:user'])->group(function () {
        Route::get('/books', [BookController::class, 'books'])->name('books');
        Route::get('/cart', [BookController::class, 'cart'])->name('cart');
        Route::post('/add-to-cart', [BookController::class, 'addToCart'])->name('cart.add');
        Route::post('/remove-from-cart', [BookController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/change-quantity', [BookController::class, 'changeQuantity'])->name('cart.quantity');
        Route::get('/empty-cart', [BookController::class, 'emptyCart'])->name('cart.empty');
    });
});

require __DIR__.'/auth.php';
