<?php

use App\Http\Controllers\Segmentations\StoreSegmentationController;

Route::prefix('segmentations')->group(function () {
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::post('store', StoreSegmentationController::class)
            ->name('segmentations.store');
    });
});
