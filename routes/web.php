<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UtilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('utils/commands', [UtilController::class, 'commands'])->name('utils.commands');

Route::get('activitylogs', [ActivityLogController::class, 'index']);
