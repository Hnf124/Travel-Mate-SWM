<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TourismController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\SearchHistoryController;
use App\Http\Controllers\WeatherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes ini sudah diberi prefix /v1 untuk semua endpoint sesuai unit test.
|
*/

Route::prefix('v1')->group(function() {

    // Auth
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);

    // Routes yang memerlukan autentikasi
    Route::middleware('auth:sanctum')->group(function(){

        Route::post('/logout',[AuthController::class,'logout']);

        // Tourism Places
        Route::get('/tourism-places',[TourismController::class,'index']);
        Route::get('/tourism-places/{id}',[TourismController::class,'show']);

        // Favorites
        Route::get('/favorites',[FavoriteController::class,'index']);
        Route::post('/favorites',[FavoriteController::class,'store']);
        Route::delete('/favorites/{id}',[FavoriteController::class,'destroy']);

        // Search History
        Route::get('/search-history',[SearchHistoryController::class,'index']);
        Route::post('/search-history',[SearchHistoryController::class,'store']);

        // Weather
        Route::get('/weather',[WeatherController::class,'show']);
    });
});