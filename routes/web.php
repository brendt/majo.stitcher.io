<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ResetMapController;
use App\Http\Controllers\SaveMenuController;
use App\Http\Controllers\TilesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomeController::class);
Route::get('/tiles', TilesController::class);
Route::get('/map/reset', ResetMapController::class);
Route::post('/map/menu/save', SaveMenuController::class);
Route::get('/map/{seed?}', MapController::class);
