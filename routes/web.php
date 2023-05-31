<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapCanvasController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MapJSController;
use App\Http\Controllers\MapPreviewController;
use App\Http\Controllers\MapStyleController;
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
Route::get('/map/tiles', TilesController::class);
Route::get('/map/reset', ResetMapController::class);
Route::post('/map/menu/save', SaveMenuController::class);
Route::get('/map/preview', MapPreviewController::class);
Route::get('/map/style', MapStyleController::class);
Route::get('/map/js', MapJSController::class);
Route::get('/map/canvas', MapCanvasController::class);
Route::get('/map/{seed?}', MapController::class);
