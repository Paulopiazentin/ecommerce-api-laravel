<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdutoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/produtos', [ProdutoController::class, 'index']);