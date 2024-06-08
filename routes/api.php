<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\UserRegisterController;

Route::post('/register', [UserRegisterController::class, 'register']);
Route::post('/checkEmail', [UserRegisterController::class, 'checkEmail']);
Route::post('/login', [UserLoginController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [UserLoginController::class, 'logout']);

Route::prefix('admin')->middleware('auth:sanctum', 'abilities:admin')->group(function () {
    Route::get('/posts', [AdminController::class, 'indexPost']);
    Route::get('/topics', [AdminController::class, 'indexTopic']);
    Route::post('/posts', [AdminController::class, 'storePost']);
    Route::post('/topics', [AdminController::class, 'storeTopic']);
    Route::post('/posts/{id}', [AdminController::class, 'destroyPost']);
    Route::post('/topics/{id}', [AdminController::class, 'destroyTopic']);
});

Route::prefix('user')->middleware('auth:sanctum', 'abilities:user')->group(function () {
    Route::get('/posts', [UserController::class, 'index']);
    Route::get('/posts/{id}', [UserController::class, 'show']);
    Route::post('/posts', [UserController::class, 'store']);
    Route::post('/posts/{id}', [UserController::class, 'destroyPost']);
    Route::get('/mypage', [UserController::class, 'mypage']);
    Route::get('/mypost', [UserController::class, 'mypost']);
    //Route::get('/users', [UserController::class, 'ranking']);
    Route::post('/review/{id}', [UserController::class, 'review']);
    Route::delete('/review/{id}', [UserController::class, 'destroyReview']);

    Route::get('/user/users', [UserController::class, 'ranking']);
    Route::get('/otheruser',[UserController::class, 'otheruser']);
    Route::get('/otheruserposts', [UserController::class, 'otheruserPosts']);
});

