<?php

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

Route::post('auth/login', 'Api\JwtAuthController@login')
    ->middleware('throttle:5,1')
    ->name('api.auth.login');

Route::post('auth/refresh', 'Api\JwtAuthController@refresh')
    ->middleware('throttle:5,1')
    ->name('api.auth.refresh');

Route::prefix('auth')
    ->middleware('jwt.auth', 'throttle:5,1')
    ->name('auth.')
    ->group(function () {
        Route::post('logout', 'Api\JwtAuthController@logout')->name('logout');
        Route::post('me', 'Api\JwtAuthController@me')->name('me');
    });

Route::apiResource('users', 'Api\UserController')
    ->only(['store'])
    ->middleware('throttle:3,1')
    ->names(['store' => 'api.users.store']);

Route::name('api.')
    ->middleware('jwt.auth')
    ->group(function () {
        Route::apiResource('users', 'Api\UserController')
            ->except(['store']);
    });

Route::apiResource('activitylogs', 'Api\ActivityLogController')
    ->only(['index', 'show']);
