<?php

use App\Filament\Loggers\UserLogger;
use Filament\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::any('/logout', function () {
    UserLogger::make(auth()->user())->logout();
    Auth::logout();
    return redirect()->route('filament.admin.pages.dashboard');
})->name('logged-sign-out');
