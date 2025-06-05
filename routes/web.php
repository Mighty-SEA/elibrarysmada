<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Middleware\CheckUserType;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Route untuk administrasi
Route::middleware(['auth', 'verified', CheckUserType::class.':admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route manajemen user
    Route::resource('user-management', UserManagementController::class)->parameters([
        'user-management' => 'user'
    ]);
});

// Route untuk murid dan guru
Route::middleware(['auth', 'verified', CheckUserType::class.':user'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
