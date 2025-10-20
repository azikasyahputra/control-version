<?php

use App\Http\Controllers\ObjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/object', [ObjectController::class, 'store']);

Route::get('/object/get_all_records', [ObjectController::class, 'index']);
Route::get('/object/{key}', [ObjectController::class, 'show']);
