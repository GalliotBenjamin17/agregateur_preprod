<?php

use App\Http\Controllers\FinancialExports\IndexFinancialExportsController;

Route::prefix('comptabilite')->group(function () {

    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {

        Route::get('', IndexFinancialExportsController::class)
            ->name('accountancy.index');

    });

});
