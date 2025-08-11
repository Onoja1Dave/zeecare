<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apiController;

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

Route::get('/userapi', [apiController::class, 'getUsers']);
Route::post('/createUser', [apiController::class, 'createUser']);
Route::delete('/deleteUser/{id}', [apiController::class, 'deleteUser']);
Route::put ('/userapi/{id}', [apiController::class, 'updateUser']);


