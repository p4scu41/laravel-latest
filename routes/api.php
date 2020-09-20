<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\JwtAuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/status', function () {
    return 'OK';
});

Route::post('auth/login', [JwtAuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('api.auth.login');

Route::post('auth/refresh', [JwtAuthController::class, 'refresh'])
    ->middleware('throttle:5,1')
    ->name('api.auth.refresh');

Route::prefix('auth')
    ->middleware('jwt.auth', 'throttle:5,1')
    ->name('auth.')
    ->group(function () {
        Route::post('logout', [JwtAuthController::class, 'logout'])->name('logout');
        Route::post('me', [JwtAuthController::class, 'me'])->name('me');
    });

Route::apiResource('users', UserController::class)
    ->only(['store'])
    ->middleware('throttle:3,1')
    ->names(['store' => 'api.users.store']);

Route::name('api.')
    ->middleware('jwt.auth')
    ->group(function () {
        Route::apiResource('users', UserController::class)
            ->except(['store']);
    });

Route::apiResource('activitylogs', ActivityLogController::class)
    ->only(['index', 'show']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
