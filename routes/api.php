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
Route::controller(AuthController::class)->prefix('auth')->group(function (){
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/refresh', 'refresh')->middleware('auth_jwt');
    Route::get('/send-reset-password-email', 'sendResetPasswordEmail');
    Route::post('/reset-password', 'resetPassword');
});



Route::controller(CategoryController::class)->prefix('categories')->group(function (){
    Route::get('/', 'index');
    Route::get('/{id}', 'view');
    Route::post('/{id}/upload-image', 'uploadImage')->middleware('admin_permission');
    Route::get('/{id}/images', 'getImage');
});

Route::controller(ProductController::class)->prefix('products')->group(function (){
    Route::get('/', 'index');
    Route::get('/search','searchByName');
    Route::get('/{id}', 'view');
    Route::post('/{id}/upload-image', 'uploadImage')->middleware('auth_jwt');
    Route::get('/{id}/images', 'getImage');
});

Route::controller(VerifyController::class)->prefix('products')->group(function (){
    Route::get('/send-verify-email','sendVerifyEmail');
    Route::post('/verify-email','verifyEmail');
});

Route::controller(UserController::class)->prefix('users')->group(function (){
    Route::get('/users', 'index');
    Route::get('/users/{id}', 'view');
    Route::post('/{id}/upload-image', 'uploadImage')->middleware('auth_jwt');
    Route::get('/{id}/images', 'getImage');
});



