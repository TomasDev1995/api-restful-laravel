<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController as V1UserController;
use App\Http\Controllers\Task\TaskController as V1TaskController;
use App\Http\Controllers\Authentication\AuthenticationController as V1AuthenticationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::post('/register', [V1AuthenticationController::class, 'register']);
    Route::post('/login', [V1AuthenticationController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [V1UserController::class, 'index']);
            Route::get('/{id}', [V1UserController::class, 'show']);
            Route::post('/', [V1UserController::class, 'store']);
            Route::put('/{id}', [V1UserController::class, 'update']);
            Route::delete('/{id}', [V1UserController::class, 'destroy']);
        });

        Route::prefix('tasks')->group(function () {
            Route::get('/', [V1TaskController::class, 'index']);
            Route::get('/{id}', [V1TaskController::class, 'show']);
            Route::post('/', [V1TaskController::class, 'store']);
            Route::put('/{id}', [V1TaskController::class, 'update']);
            Route::delete('/{id}', [V1TaskController::class, 'destroy']);
        });
        
        Route::post('/logout', [V1AuthenticationController::class, 'logout']);
    });
});
