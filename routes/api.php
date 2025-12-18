<?php

use App\Http\Controllers\Api\WeatherApiController;
use App\Http\Controllers\FavoriteCityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test route
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working!',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Public Weather API Routes (NO AUTH REQUIRED)
Route::prefix('weather')->group(function () {
    Route::get('/current', [WeatherApiController::class, 'getCurrentWeather']);
    Route::get('/search-cities', [WeatherApiController::class, 'searchCities']);
});

// Authenticated API Routes (Web Session Auth)
Route::middleware('web')->group(function () {
    Route::middleware('auth')->group(function () {
        // Favorites API
        Route::prefix('favorites')->group(function () {
            Route::post('/check', [FavoriteCityController::class, 'checkFavorite']);
            Route::post('/add', [FavoriteCityController::class, 'store']);
            Route::post('/remove', [FavoriteCityController::class, 'destroy']);
            Route::post('/update-order', [FavoriteCityController::class, 'updateOrder']);
        });
    });
});
