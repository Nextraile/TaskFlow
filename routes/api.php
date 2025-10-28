<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PasswordResetLinkController;

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

Route::prefix('forgot-password')->group(function () {
    Route::get('/', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');
    Route::post('/', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');
});

Route::prefix('reset-password')->group(function () {
    Route::get('{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');
    Route::post('/', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');
});