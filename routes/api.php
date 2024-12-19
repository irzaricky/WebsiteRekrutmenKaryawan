<?php

use App\Http\Controllers\ApiAuthController;

Route::group(['middleware' => ['api']], function () {
    // Public routes
    Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login']);

    // Protected routes
    Route::group(['middleware' => ['jwt.auth']], function () {
        Route::get('user', [ApiAuthController::class, 'getAuthenticatedUser']);
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::post('refresh', [ApiAuthController::class, 'refresh']);
    });
});