
<?php

use Illuminate\Support\Facades\Route;

// Home Page
Route::get('/', function () {
    return view('home');
})->name('home');

// Screen 1: Explore Art Catalog
// Assigned to Vinz Jerby T. Acaylar
Route::get('/explore', function () {
    return view('explore');
})->name('explore');

// Screen 2: View Product Detail
// Assigned to Lauren Jane D. Isleta
Route::get('/product/{id?}', function ($id = 1) {
    return view('product', ['id' => $id]);
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
