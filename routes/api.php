<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        
        Route::middleware('throttle:3,1')->group(function () {
            Route::post('register', [AuthController::class, 'register']);
            Route::post('login',    [AuthController::class, 'login']);
        });

        // Protected auth routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me',      [AuthController::class, 'me']);
            Route::put('profile',           [AuthController::class, 'updateProfile']);
            Route::put('change-password',   [AuthController::class, 'changePassword']);
            Route::delete('delete-account', [AuthController::class, 'deleteAccount']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('posts', PostController::class);
        Route::get('posts-stats', [PostController::class, 'stats']);
    });
});


/*
|--------------------------------------------------------------------------
| END of API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found.',
    ], 404);
});