<?php

use App\Http\Controllers\Tenants\Details\ShowTenantController;
use App\Http\Controllers\Tenants\Details\ShowTenantDetailsController;
use App\Http\Controllers\Tenants\IndexTenantController;
use App\Http\Controllers\Tenants\StoreTenantController;

Route::prefix('instances')->group(function () {
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('', IndexTenantController::class)
            ->name('tenants.index');

        Route::post('store', StoreTenantController::class)
            ->name('tenants.store');

        Route::prefix('{tenant:slug}')->group(function () {
            Route::get('', ShowTenantController::class)
                ->name('tenants.show');

            Route::get('details', ShowTenantDetailsController::class)
                ->name('tenants.show.details');
        });
    });
});
