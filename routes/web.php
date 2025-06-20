<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\ProductsChangeController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TestClientsConnectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/', function () {
    return redirect()->route('links');
  })->name('home');

  Route::get('links', [LinkController::class, 'index'])->name('links');
  Route::get('links/create', fn() => inertia()->render('Links/Create'))->name('links.create');
  Route::post('links', [LinkController::class, 'store'])->name('links.store');

  Route::get('products', [ProductsController::class, 'index'])->name('products');
  Route::get('products/change', [ProductsChangeController::class, 'index'])->name('products.change');


  Route::get('product/check/{product}', [ProductsController::class, 'check'])->name('product.check');
});


require __DIR__ . '/auth.php';
