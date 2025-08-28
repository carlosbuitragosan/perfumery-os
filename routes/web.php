<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::assertRedirect('/', '/dashboard');

// Dashboard
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Materials (temp)
    Route::get('/materials', fn () => 'ok')->name('materials.index');
});

require __DIR__.'/auth.php';
