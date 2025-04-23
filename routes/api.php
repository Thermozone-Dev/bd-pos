<?php

use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\DiscountController;
use App\Http\Controllers\Api\v1\ItemController;
use App\Http\Controllers\Api\v1\JournalController;
use App\Http\Controllers\Api\v1\PackageController;
use App\Http\Controllers\Api\v1\PaymentMethodController;
use App\Http\Controllers\Api\v1\ProductController;
use App\Http\Controllers\Api\v1\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth'], function () {
    Route::get('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/tokens', [AuthController::class, 'checkTokens']);
        Route::get('/user', [AuthController::class, 'show']);
    });
});


Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    // Journal Routes
    Route::get('/journal', [JournalController::class, 'newTransaction']);
    Route::get('/journal/{id}', [JournalController::class, 'updateTransaction']);

    // Item Routes
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    Route::get('/itemProducts', [ItemController::class, 'products']);
    Route::get('/itemProducts/{id}', [ItemController::class, 'product']);
    Route::get('/itemPackages', [ItemController::class, 'packages']);
    Route::get('/itemPackages/{id}', [ItemController::class, 'package']);

    // Product Routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Package Routes
    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('/packages/{id}', [PackageController::class, 'show']);
    Route::post('/packages', [PackageController::class, 'store']);
    Route::put('/packages/{id}', [PackageController::class, 'update']);
    Route::delete('/packages/{id}', [PackageController::class, 'destroy']);

    // Discount Routes
    Route::get('/discounts', [DiscountController::class, 'index']);
    Route::get('/discounts/{id}', [DiscountController::class, 'show']);
    Route::post('/discounts', [DiscountController::class, 'store']);
    Route::put('/discounts/{id}', [DiscountController::class, 'update']);
    Route::delete('/discounts/{id}', [DiscountController::class, 'destroy']);

    // Transaction Routes
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

    // PaymentMethod Routes
    Route::get('/paymentmethods', [PaymentMethodController::class, 'index']);
    Route::get('/paymentmethods/{id}', [PaymentMethodController::class, 'show']);
    Route::post('/paymentmethods', [PaymentMethodController::class, 'store']);
    Route::put('/paymentmethods/{id}', [PaymentMethodController::class, 'update']);
    Route::delete('/paymentmethods/{id}', [PaymentMethodController::class, 'destroy']);
});
