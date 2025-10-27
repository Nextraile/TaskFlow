<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])
    ->name('register');
    Route::post('login', [AuthController::class, 'login'])
    ->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user'])
    ->name('user');
    Route::delete('logout', [AuthController::class, 'logout'])
    ->name('logout');
});
