<?php

use App\Http\Controllers\News\IndexNewsController;

Route::prefix('actualités')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {
        Route::get('', IndexNewsController::class)
            ->name('news.index');
    });
});
