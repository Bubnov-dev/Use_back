<?php

use App\Http\Controllers\AuthConroller;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthConroller::class)->group(function () {
    Route::post('/test', 'test');
    Route::post('/registerLogin', 'registerLogin');
    Route::post('/submitCode', 'submitCode');
    Route::post('/submitRegister', 'submitRegister')->middleware('auth:sanctum');;
    Route::post('/getMe', 'getMe')->middleware('auth:sanctum');
});
