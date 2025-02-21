<?php

// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PostController;

// مسار لتسجيل الدخول (متاح للجميع)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



// مجموعة المسارات المحمية بواسطة Sanctum (يجب إرسال التوكن في كل طلب)
Route::middleware('auth:sanctum')->group(function () {

    // مسارات إدارة المستخدمين (خاصة بالمسؤول فقط)
    Route::prefix('v1')->group(function () {
        // مسارات إدارة المستخدمين (خاصة بالمسؤول فقط)
        Route::middleware('role:admin')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::get('/users/get_user/{id}', [UserController::class, 'show']);
            Route::put('/users/edit_user/{id}', [UserController::class, 'update']);
            Route::delete('/users/delete_user/{id}', [UserController::class, 'destroy']);

        });


    // مسارات إدارة المنشورات (خاصة بالمستخدمين)
    // يمكنك السماح فقط للمستخدمين (role:user)، أو إذا أردت أن يتمكن المسؤولون أيضًا من النشر، استخدم (role:user|admin)
    Route::middleware('role:user|admin')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('posts/get_post/{id}', [PostController::class, 'show']);
        Route::put('posts/edit_posts/{id}', [PostController::class, 'update']);
        Route::delete('posts/delete_post/{id}', [PostController::class, 'destroy']);
    });

    // مسار تسجيل الخروج
    Route::post('/logout', [AuthController::class, 'logout']);
});
});
