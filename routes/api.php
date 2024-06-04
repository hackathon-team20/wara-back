<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\UserLoginController;
use App\Http\Controllers\UserRegisterController;

Route::post('/register', [UserRegisterController::class, 'register']);

Route::get('/admin/posts', [AdminController::class, 'indexPost']);
Route::post('/admin/posts', [AdminController::class, 'storePost']);
Route::post('/admin/topics', [AdminController::class, 'storeTopic']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
