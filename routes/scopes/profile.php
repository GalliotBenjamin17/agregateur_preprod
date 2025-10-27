<?php

use App\Http\Controllers\Profile\SendResetPasswordLinkController;
use App\Http\Controllers\Profile\ShowDatasController;
use App\Http\Controllers\Profile\ShowInformationsController;
use App\Http\Controllers\Profile\ShowSecurityController;

Route::prefix('profile')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('', ShowInformationsController::class)
            ->name('profile.show');

        Route::get('datas', ShowDatasController::class)
            ->name('profile.datas');

        Route::get('security', ShowSecurityController::class)
            ->name('profile.security');

        Route::post('update-password', SendResetPasswordLinkController::class)
            ->name('profile.reset-password');
    });
});
