<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CandidateController;
use App\Http\Controllers\Api\CandidateCreationController;
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

Route::post('/auth', [AuthController::class, 'generateAccessToken']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('/lead', [CandidateCreationController::class, 'create']);
    Route::get('/lead/{id}', [CandidateController::class, 'show']);
    Route::get('/leads', [CandidateController::class, 'showAll']);
});

