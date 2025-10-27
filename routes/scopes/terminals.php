<?php

use App\Http\Controllers\Terminals\StoreTerminalController;

Route::prefix('terminals')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {
        Route::post('store', StoreTerminalController::class)
            ->name('terminals.store');
    });
});
