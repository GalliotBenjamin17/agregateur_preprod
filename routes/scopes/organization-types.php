<?php

use App\Http\Controllers\OrganizationTypes\Details\ShowOrganizationTypeController;
use App\Http\Controllers\OrganizationTypes\StoreOrganizationTypeLinkController;
use App\Http\Controllers\OrganizationTypes\StoreOrganizationTypesController;

Route::prefix('types-organisation')->group(function () {
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::post('store', StoreOrganizationTypesController::class)
            ->name('organization-types.store');

        Route::prefix('{organizationType:slug}')->group(function () {
            Route::get('', ShowOrganizationTypeController::class)
                ->name('organization-types.show');

            Route::prefix('lien')->group(function () {
                Route::post('store', StoreOrganizationTypeLinkController::class)
                    ->name('organization-types.links.store');
            });
        });
    });
});
