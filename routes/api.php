<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\LoginController;
use App\Http\Controllers\api\GameController;
use App\Http\Controllers\api\RankController;

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

Route::post('/login', [LoginController::class, 'login']);
Route::post('/players', [UserController::class, 'store']);

Route::middleware('auth:api')->prefix('players')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::post('/{user}/games', [GameController::class, 'play']);
    Route::delete('/{user}/games', [GameController::class, 'delete']);
    Route::get('/{user}/games', [GameController::class, 'index']);
    Route::get('/ranking', [RankController::class, 'rank']);
    Route::get('/ranking/loser', [RankController::class, 'loser']);
    Route::get('/ranking/winner', [RankController::class, 'winner']);
});