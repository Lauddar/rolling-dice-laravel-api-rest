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

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware(['cors'])->group(function () {
    Route::post('/players', [UserController::class, 'store'])->name('register');
});

//Admin permission routes
Route::middleware(['auth:api', 'cors', 'role:Admin'])->prefix('players')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('players.index');
    Route::get('/ranking', [RankController::class, 'rank'])->name('admin.ranking');
    Route::get('/ranking/loser', [RankController::class, 'loser'])->name('admin.ranking.loser');
    Route::get('/ranking/winner', [RankController::class, 'winner'])->name('admin.ranking.winner');
});

//Player permission routes
Route::middleware(['auth:api', 'cors'])->prefix('players')->group(function () {
    Route::put('/{user}', [UserController::class, 'update']);
    Route::get('/{user}', [UserController::class, 'getUser']);
    Route::post('/{user}/games', [GameController::class, 'play']);
    Route::delete('/{user}/games', [GameController::class, 'delete']);
    Route::get('/{user}/games', [GameController::class, 'index']);
});
