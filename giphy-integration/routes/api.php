<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GifController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/gif/search', [GifController::class, 'searchGifs'])->name('gif.search');
    Route::get('/gif/{id}', [GifController::class, 'getGifById'])->name('gif.get');
    Route::post('/gif/favorite/store', [GifController::class, 'saveFavoriteGif'])->name('gif.store');
});
