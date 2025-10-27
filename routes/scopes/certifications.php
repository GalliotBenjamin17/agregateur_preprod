<?php

use App\Http\Controllers\Certifications\StoreCertificationController;

Route::prefix('certifications')->group(function () {
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::post('store', StoreCertificationController::class)
            ->name('certifications.store');
    });
});
