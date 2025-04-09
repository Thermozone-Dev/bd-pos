<?php

use App\Http\Controllers\JournalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Journal Routes
Route::get('/journal', [JournalController::class, 'newTransaction'])->name('journal-log-transaction');
Route::get('/journal/{id}', [JournalController::class, 'updateTransaction'])->name('journal-update-transaction');
