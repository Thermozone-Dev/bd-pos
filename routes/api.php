<?php

use App\Http\Controllers\Api\v1\ItemController;
use App\Http\Controllers\Api\v1\JournalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Journal Routes
Route::get('/v1/journal', [JournalController::class, 'newTransaction']);
Route::get('/v1/journal/{id}', [JournalController::class, 'updateTransaction']);

// Item Routes
Route::get('/v1/items', [ItemController::class, 'index']);
Route::get('/v1/items/{id}', [ItemController::class, 'show']);
Route::post('/v1/items', [ItemController::class, 'store']);
Route::put('/v1/items/{id}', [ItemController::class, 'update']);
Route::delete('/v1/items/{id}', [ItemController::class, 'destroy']);
