<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\UserRegisterController;

Route::post('/register', [UserRegisterController::class, 'register']);
Route::post('/login', [UserLoginController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [UserLoginController::class, 'logout']);

Route::get('/admin/posts', [AdminController::class, 'indexPost']);
Route::get('/admin/topics', [AdminController::class, 'indexTopic']);
Route::post('/admin/posts', [AdminController::class, 'storePost']);
Route::post('/admin/topics', [AdminController::class, 'storeTopic']);
Route::post('/admin/posts/{id}', [AdminController::class, 'destroyPost']);
Route::post('/admin/topics/{id}', [AdminController::class, 'destroyTopic']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
