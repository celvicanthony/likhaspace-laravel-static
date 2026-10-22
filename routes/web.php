<?php

use Illuminate\Support\Facades\Route;

// Redirect root to explore catalog
Route::get('/', function () {
    return redirect('/explore');
});

// Screen 1: Explore Art Catalog
Route::get('/explore', function () {
    return view('explore');
})->name('explore');

// Screen 2: View Product Detail (Named as both product.show AND client.show)
Route::get('/product/{id?}', function ($id = 1) {
    return view('product', ['id' => $id]);
})->name('product.show');

Route::get('/obra/{id?}', function ($id = 1) {
    return view('product', ['id' => $id]);
})->name('client.show');

// Screen 3: Admin Dashboard
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Screen 4: Admin Artwork Inventory
Route::get('/admin/inventory', function () {
    return view('admin.inventory');
})->name('admin.inventory');