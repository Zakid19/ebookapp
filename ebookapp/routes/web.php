<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EbookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Semua route yang butuh login pakai auth middleware
Route::middleware(['auth'])->group(function () {

    // Dashboard + Ebook CRUD
    Route::get('/dashboard', [EbookController::class, 'index'])->name('dashboard');

    Route::post('/ebooks/upload', [EbookController::class, 'upload'])->name('ebooks.upload');
    Route::get('/ebooks/view/{ebook}', [EbookController::class, 'view'])->name('ebooks.view');
    Route::get('/ebooks/stream/{ebook}', [EbookController::class, 'stream'])->name('ebook.stream'); // cukup sekali, auth sudah termasuk
    Route::delete('/ebooks/{ebook}', [EbookController::class, 'delete'])->name('ebooks.delete');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
