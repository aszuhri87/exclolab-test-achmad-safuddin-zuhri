<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\ApiHandling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware(ApiHandling::class);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verification/{email}', [AuthController::class, 'verify']);
Route::post('/forgot-password/{email}', [AuthController::class, 'forgot_password']);
Route::get('/verify-token/{token}', [AuthController::class, 'verify_token']);
Route::put('/update-password/{token}', [AuthController::class, 'update_password']);


