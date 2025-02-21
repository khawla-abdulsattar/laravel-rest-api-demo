<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PostController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('v1')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::get('/users/get_user/{id}', [UserController::class, 'show']);
            Route::put('/users/edit_user/{id}', [UserController::class, 'update']);
            Route::delete('/users/delete_user/{id}', [UserController::class, 'destroy']);

        });


    Route::middleware('role:user|admin')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('posts/get_post/{id}', [PostController::class, 'show']);
        Route::put('posts/edit_posts/{id}', [PostController::class, 'update']);
        Route::delete('posts/delete_post/{id}', [PostController::class, 'destroy']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
});
