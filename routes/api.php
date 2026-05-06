<?php

use App\Http\Controllers\Api\V1\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:Administrator'])->group(function () {
    Route::apiResource('roles', RoleController::class);
});
