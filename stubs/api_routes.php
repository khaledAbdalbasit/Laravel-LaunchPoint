<?php

use Illuminate\Support\Facades\Route;

// LaunchPoint Routes
Route::prefix('api')->group(function () {
    Route::get('profile', [\App\Http\Controllers\Api\ProfileController::class, 'index']);
    Route::get('settings', [\App\Http\Controllers\Api\SettingsController::class, 'index']);
    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
});
