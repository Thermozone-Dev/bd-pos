<?php

use App\Filament\Loggers\UserLogger;
use App\Http\Controllers\Api\v1\TransactionController;
use Filament\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::any('/logout', function () {
    UserLogger::make(auth()->user())->logout();
    Auth::logout();
    return redirect()->route('filament.admin.pages.dashboard');
})->name('logged-sign-out');

// Route::get('/test-controller/transaction',[TransactionController::class,'dailySummary']);

