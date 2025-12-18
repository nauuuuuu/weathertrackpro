<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\FavoriteCityController;
use App\Http\Controllers\SearchHistoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [WeatherController::class, 'index'])->name('home');

// Auth required routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // Favorite Cities
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteCityController::class, 'index'])->name('index');
        Route::delete('/{favorite}', [FavoriteCityController::class, 'destroyById'])->name('destroy');
    });
    
    // Search History
    Route::prefix('history')->name('history.')->group(function () {
        Route::get('/', [SearchHistoryController::class, 'index'])->name('index');
        Route::delete('/{history}', [SearchHistoryController::class, 'destroy'])->name('destroy');
        Route::delete('/', [SearchHistoryController::class, 'destroyAll'])->name('destroyAll');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::post('/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('toggleActive');
    });
});

require __DIR__.'/auth.php';
