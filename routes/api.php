<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\UserController;

Route::get('/admin/posts', [AdminController::class, 'indexPost']);
Route::post('/admin/posts', [AdminController::class, 'store']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
