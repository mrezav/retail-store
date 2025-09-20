<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VariantController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
    // Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    // Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    // Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    // Route::get('categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    // Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    // Route::delete('categories/{id}', [CategoryController::class, 'delete'])->name('categories.delete');
    
    Route::resource('categories', CategoryController::class);

    Route::resource('products', ProductController::class);

    Route::resource('variants', VariantController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
