<?php

use App\Http\Controllers\Api\PokemonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('pokemon')->group(function() {
    Route::get('/', [PokemonController::class, 'index']);
    Route::get('/{id}', [PokemonController::class, 'detail']);
});
