<?php

use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Dashboard
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Materials
    Route::resource('materials', MaterialController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    // bottles
    Route::resource('materials.bottles', BottleController::class)->only(['create']);
});

require __DIR__.'/auth.php';
