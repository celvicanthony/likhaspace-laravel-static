<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtworkController;
// Home Page
Route::get('/', function () {
    return view('home');
})->name('home');

// Explore Art Catalog
Route::get('/explore', [ArtworkController::class, 'index'])
    ->name('explore');

// View Product Detail
Route::get('/product/{id?}', function ($id = null) {
    if ($id === null) {
        return redirect()->route('explore');
    }

    return app(ArtworkController::class)->show($id);
})->name('product.show');

// Screen 3: Admin Dashboard
// Assigned to Janessa Mae C. Ramos
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Screen 4: Admin Artwork Inventory
// Assigned to Janessa Mae C. Ramos
Route::get('/admin/inventory', function () {
    return view('admin.inventory');
})->name('admin.inventory');

// Favorites
Route::get('/favorites', function () {
    return view('favorites');
})->name('favorites');

// Shopping Cart
Route::get('/cart', function () {
    return view('cart');
})->name('cart');

// Checkout
Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');
