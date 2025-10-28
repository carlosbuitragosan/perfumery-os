<?php

use App\Http\Controllers\BottleController;
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
    Route::resource('materials', MaterialController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    // bottles
    Route::resource('materials.bottles', BottleController::class)
        ->only(['create', 'store']);

    // update bottle to finished
    Route::post('/bottles/{bottle}/finish', [BottleController::class, 'finish'])
        ->name('bottles.finish');
    // edit bottle
    Route::get('/bottles/{bottle}/edit', [BottleController::class, 'edit'])
        ->name('bottles.edit');
});

require __DIR__.'/auth.php';
