<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AdminAuthController;

Route::prefix('v1')->group(function () {
    
    // Public routes
    Route::post('/login', [AdminAuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AdminAuthController::class, 'me']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);

        // Only Super Admins can access these routes
        Route::middleware('role:Super Admin')->group(function () {
            // Example: Route::apiResource('tenants', TenantController::class);
        });
    });
});
