<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',      [AuthController::class, 'logout']);
    Route::get('/wallet',       [WalletController::class, 'index']);
    Route::post('/topup',       [WalletController::class, 'topup']);
    Route::post('/transfer',    [WalletController::class, 'transfer']);
    Route::get('/transactions', [TransactionController::class, 'index']);
});