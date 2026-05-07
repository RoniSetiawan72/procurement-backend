<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'role:Administrator'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('roles', RoleController::class);
    });
});
