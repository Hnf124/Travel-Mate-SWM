<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TourismController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\SearchHistoryController;
use App\Http\Controllers\WeatherController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);

    Route::get('/tourism-places',[TourismController::class,'index']);
    Route::get('/tourism-places/{id}',[TourismController::class,'show']);

    Route::get('/favorites',[FavoriteController::class,'index']);
    Route::post('/favorites',[FavoriteController::class,'store']);
    Route::delete('/favorites/{id}',[FavoriteController::class,'destroy']);

    Route::get('/search-history',[SearchHistoryController::class,'index']);
    Route::post('/search-history',[SearchHistoryController::class,'store']);
	
	Route::post('/register', [AuthController::class,'register']);
	Route::post('/login', [AuthController::class,'login']);

    Route::get('/weather',[WeatherController::class,'show']);
});