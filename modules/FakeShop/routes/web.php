<?php

use Illuminate\Support\Facades\Route;
use FakeShop\Controller\FakeShopController;

Route::get('/fake-shop', [FakeShopController::class, 'index'])->name('fake-shop.index');
