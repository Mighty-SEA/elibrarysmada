<?php

use App\Http\Controllers\BookshelvesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Middleware\CheckUserType;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home route using controller
Route::get('/', [\App\Http\Controllers\BookController::class, 'home'])->name('home');

// Book detail route
Route::get('/book/{book}', [\App\Http\Controllers\BookController::class, 'detail'])->name('book.detail');

// API routes that don't need authentication
Route::get('/api/books/all-ids', [\App\Http\Controllers\BookController::class, 'getAllBookIds'])->name('api.books.all-ids');

// Route untuk loans/peminjaman
Route::middleware(['auth', 'verified'])->group(function () {
    // User loans routes
    Route::get('/loans', [\App\Http\Controllers\LoanController::class, 'userLoans'])->name('loans.user');
    Route::post('/loans/request', [\App\Http\Controllers\LoanController::class, 'requestLoan'])->name('loans.request');
    Route::delete('/loans/{loan}/cancel', [\App\Http\Controllers\LoanController::class, 'cancelRequest'])->name('loans.cancel');
});

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
    Route::post('books/bulk-update-jumlah', [BookController::class, 'bulkUpdateJumlah'])->name('books.bulk-update-jumlah');
    Route::get('books/all-ids', [BookController::class, 'getAllBookIds'])->name('books.all-ids');
    
    // Routes for loan management (admin only)
    Route::get('/loans', [\App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/pending', [\App\Http\Controllers\LoanController::class, 'pendingApproval'])->name('loans.pending');
    Route::get('/loans/active', [\App\Http\Controllers\LoanController::class, 'activeLoans'])->name('loans.active');
    Route::post('/loans/{loan}/approve', [\App\Http\Controllers\LoanController::class, 'approveLoan'])->name('loans.approve');
    Route::post('/loans/{loan}/return', [\App\Http\Controllers\LoanController::class, 'returnBook'])->name('loans.return');
    Route::delete('/loans/{loan}/reject', [\App\Http\Controllers\LoanController::class, 'rejectRequest'])->name('loans.reject');
});

// Route untuk murid dan guru
Route::middleware(['auth', 'verified', CheckUserType::class.':user'])->group(function () {
    Route::get('/bookshelves', [BookshelvesController::class, 'index'])->name('bookshelves');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
