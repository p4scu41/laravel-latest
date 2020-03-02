<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('users', 'Api\UserController')
    ->only(['store'])
    ->middleware('throttle:3,1')
    ->names(['store' => 'api.users.store']);

Route::namespace('Api')
    ->name('api.')
    ->middleware('throttle:60,1')
    ->group(function () {
        Route::apiResource('users', 'UserController')
            ->except(['store']);
    });
