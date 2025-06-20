<?php

use App\Http\Controllers\API\ProductsApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('validate.email')->prefix('v1')->group(function () {
  Route::get('products', [ProductsApiController::class, 'index']);
});
