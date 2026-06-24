<?php

use App\Http\Controllers\Api\PortivaApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('portiva')->middleware('web')->group(function () {
    Route::get('/profiles', [PortivaApiController::class, 'profiles']);
    Route::post('/profiles', [PortivaApiController::class, 'store']);
    Route::get('/profiles/{id}', [PortivaApiController::class, 'show']);
    Route::put('/profiles/{id}', [PortivaApiController::class, 'update']);
    Route::delete('/profiles/{id}', [PortivaApiController::class, 'destroy']);
    Route::get('/account', [PortivaApiController::class, 'account']);
    Route::get('/users', [PortivaApiController::class, 'users']);
});
