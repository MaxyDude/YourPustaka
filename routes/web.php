<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Books routes
    Route::resource('books', BookController::class);

    // Loans routes
    Route::resource('loans', LoanController::class);
    // Temporary borrow route that accepts book data via query params (no DB required)
    Route::get('/loans/borrow', [LoanController::class, 'showBorrowTemp'])->name('loans.borrow.temp');
    Route::get('/loans/{book}/borrow', [LoanController::class, 'showBorrowForm'])->name('loans.borrow');
    Route::get('/pinjaman/detail/{book_id}', [LoanController::class, 'showDetail'])->name('loans.detail');
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/return', [LoanController::class, 'processReturn'])->name('loans.return');
    Route::get('/loans/pending', [LoanController::class, 'pending'])->name('loans.pending');
    Route::get('/loans-return', [LoanController::class, 'returnForm'])->name('loans.return-form');
    Route::post('/loans/scan-barcode', [LoanController::class, 'scanBarcode'])->name('loans.scan-barcode');

    // Reviews routes
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/book/{book_id}', [ReviewController::class, 'getBookReviews'])->name('reviews.getBookReviews');

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/cari-tiket', function () {
            return view('admin.cari_tiket');
        })->name('admin.cari-tiket');
        Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
        Route::get('/admin/categories/json', [AdminController::class, 'getCategoriesJson'])->name('admin.categories.json');
        Route::post('/admin/categories/store', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
        Route::put('/admin/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
        Route::delete('/admin/categories/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');
        Route::post('/admin/books/store', [AdminController::class, 'storeBook'])->name('admin.books.store');
        Route::delete('/admin/books/{id}', [AdminController::class, 'deleteBook'])->name('admin.books.delete');
        Route::post('/admin/users/store', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::get('/admin/kelola-ulasan', [AdminController::class, 'kelolaUlasan'])->name('admin.kelola-ulasan');
        Route::get('/admin/ulasan-buku/{bookId}', [AdminController::class, 'getUlasanBuku'])->name('admin.ulasan-buku');
        Route::get('/admin/reports/{type}/print', [AdminController::class, 'printReport'])->name('admin.reports.print');
    });

    // Petugas routes
    Route::middleware('role:petugas')->group(function () {
        Route::get('/petugas/dashboard', [AdminController::class, 'dashboard'])->name('petugas.dashboard');
        Route::get('/petugas/cari-tiket', function () {
            return view('petugas.cari_tiket');
        })->name('petugas.cari-tiket');
        Route::get('/petugas/categories', [AdminController::class, 'categories'])->name('petugas.categories');
        Route::get('/petugas/categories/json', [AdminController::class, 'getCategoriesJson'])->name('petugas.categories.json');
        Route::post('/petugas/categories/store', [AdminController::class, 'storeCategory'])->name('petugas.categories.store');
        Route::put('/petugas/categories/{id}', [AdminController::class, 'updateCategory'])->name('petugas.categories.update');
        Route::delete('/petugas/categories/{id}', [AdminController::class, 'deleteCategory'])->name('petugas.categories.delete');
        Route::post('/petugas/books/store', [AdminController::class, 'storeBook'])->name('petugas.books.store');
        Route::delete('/petugas/books/{id}', [AdminController::class, 'deleteBook'])->name('petugas.books.delete');
        Route::get('/petugas/kelola-ulasan', [AdminController::class, 'kelolaUlasan'])->name('petugas.kelola-ulasan');
        Route::get('/petugas/ulasan-buku/{bookId}', [AdminController::class, 'getUlasanBuku'])->name('petugas.ulasan-buku');
        Route::get('/petugas/reports/{type}/print', [AdminController::class, 'printReport'])->name('petugas.reports.print');
    });
});

require __DIR__.'/auth.php';
