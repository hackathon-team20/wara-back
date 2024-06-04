<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/admin/posts', [AdminController::class, 'indexPost']);
Route::post('/admin/posts', [AdminController::class, 'storePost']);
Route::post('/admin/topics', [AdminController::class, 'storeTopic']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
