<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyController;

// Route::controller(AuthController::class)->group(function () {
//     Route::post('login', 'login')->middleware('auth_jwt');
//     Route::post('register', 'register');
//     Route::post('logout', 'logout');
//     Route::post('refresh', 'refresh');

// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth_jwt');
Route::get('/send-reset-password-email', [AuthController::class, 'sendResetPasswordEmail']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);



Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'view']);

Route::get('/products', [ProductController::class, 'index']);


Route::get('/send-verify-email',[VerifyController::class, 'sendVerifyEmail']);
Route::post('/verify-email',[VerifyController::class, 'verifyEmail']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'view']);


