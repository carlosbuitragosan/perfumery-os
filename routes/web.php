<?php

use App\Http\Controllers\BottleController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Dashboard
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth'])
    ->name('dashboard');

// Authenticated routes
Route::middleware('auth')
    ->group(function () {
        // Profile
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'edit')->name('profile.edit');
            Route::patch('/profile', 'update')->name('profile.update');
            Route::delete('/profile', 'destroy')->name('profile.destroy');
        });

        // Materials
        Route::resource('materials', MaterialController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        // New bottle for a given material
        Route::resource('materials.bottles', BottleController::class)
            ->only(['create', 'store']);

        // editing / finishing an existing bottle
        Route::prefix('bottles/{bottle}')
            ->controller(BottleController::class)
            ->group(function () {
                Route::get('/edit', 'edit')->name('bottles.edit');
                Route::patch('/', 'update')->name('bottles.update');
                Route::post('/finish', 'finish')->name('bottles.finish');
            });
    });

require __DIR__.'/auth.php';
