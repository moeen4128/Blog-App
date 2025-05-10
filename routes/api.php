<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum', 'CheckUserRole']], function () {
 Route::get('/posts', [PostController::class, 'index']); 
Route::get('/posts/{id}', [PostController::class, 'show']); 
Route::get('/posts/filter', [PostController::class, 'filterByCategory']);

Route::post('/comments', [CommentController::class, 'store']);
Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});

Route::group(['middleware' => ['auth:sanctum', 'CheckAdminRole']], function () {
 Route::post('/posts', [PostController::class, 'store']); 
 Route::post('/posts/{id}', [PostController::class, 'update']);
 Route::delete('/posts/{id}', [PostController::class, 'destroy']); 

   Route::put('/comments/{id}/status', [CommentController::class, 'updateStatus']);

   Route::get('/users', [UserController::class, 'index']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
