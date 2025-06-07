<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\BookController;
use App\Http\Middleware\CheckUserType;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Route untuk administrasi
Route::prefix('admin')->middleware(['auth', 'verified', CheckUserType::class.':admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route manajemen user
    Route::resource('user-management', UserManagementController::class)->parameters([
        'user-management' => 'user'
    ]);
    
    // Route manajemen buku
    Route::resource('books', BookController::class);
    
    // Bulk actions untuk manajemen buku
    Route::get('books/export', [BookController::class, 'export'])->name('books.export');
    Route::post('books/import', [BookController::class, 'import'])->name('books.import');
    Route::delete('books/bulk-delete', [BookController::class, 'bulkDelete'])->name('books.bulk-delete');
    Route::put('books/bulk-update-jumlah', [BookController::class, 'bulkUpdateJumlah'])->name('books.bulk-update-jumlah');
    Route::get('books/all-ids', [BookController::class, 'getAllBookIds'])->name('books.all-ids');
});

// Route untuk murid dan guru
Route::middleware(['auth', 'verified', CheckUserType::class.':user'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
});

Route::get('/', [\App\Http\Controllers\BookController::class, 'home'])->name('home');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
