<?php

use App\Http\Controllers\ProjectCarbonPrice\StoreProjectCarbonPriceController;

Route::prefix('project-carbon-price')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {

        Route::prefix('{project:slug}')->group(function () {

            Route::post('store', StoreProjectCarbonPriceController::class)
                ->name('project-carbon-price.store');

        });

    });
});
