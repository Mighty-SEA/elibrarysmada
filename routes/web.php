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

// Route untuk administrasi
Route::prefix('admin')->middleware(['auth', 'verified', CheckUserType::class.':admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    
    // Route manajemen user
    Route::resource('user-management', UserManagementController::class)->parameters([
        'user-management' => 'user'
    ]);
    
    // Route manajemen buku
    Route::resource('books', BookController::class);
    
    // Buat route export yang khusus dan mudah diakses
    Route::get('export-books', [BookController::class, 'exportBooks'])->name('export.books');
    
    // Bulk actions untuk manajemen buku
    Route::post('books/import', [BookController::class, 'import'])->name('books.import');
    Route::delete('books/bulk-delete', [BookController::class, 'bulkDelete'])->name('books.bulk-delete');
    Route::post('books/bulk-update-jumlah', [BookController::class, 'bulkUpdateJumlah'])->name('books.bulk-update-jumlah');
    Route::get('books/all-ids', [BookController::class, 'getAllBookIds'])->name('books.all-ids');
    
    // Routes for loan management (admin only)
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/pending', [LoanController::class, 'pendingApproval'])->name('loans.pending');
    Route::get('/loans/active', [LoanController::class, 'activeLoans'])->name('loans.active');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans/store', [LoanController::class, 'storeLoan'])->name('loans.store');
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approveLoan'])->name('loans.approve');
    Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::delete('/loans/{loan}/reject', [LoanController::class, 'rejectRequest'])->name('loans.reject');
});

// Route untuk murid dan guru (non-admin)
Route::middleware(['auth', 'verified', CheckUserType::class.':user'])->group(function () {
    // Bookshelves route
    Route::get('/bookshelves', [BookshelvesController::class, 'index'])->name('bookshelves');
    
    // Keep original route names but change the controller and paths
    Route::post('/bookshelves/loans/request', [BookshelvesController::class, 'requestLoan'])->name('loans.request');
    Route::delete('/bookshelves/loans/{loan}/cancel', [BookshelvesController::class, 'cancelRequest'])->name('loans.cancel');
    
    // Redirect from direct /loans path to /bookshelves for security
    Route::get('/loans', function() {
        return redirect()->route('bookshelves');
    });
});

// Bulk actions untuk manajemen buku
Route::get('/admin/books/export', [BookController::class, 'export'])->name('books.export')->middleware(['auth', 'verified', CheckUserType::class.':admin']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
