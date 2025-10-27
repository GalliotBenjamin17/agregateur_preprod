<?php

use App\Http\Controllers\Comments\DeleteCommentController;
use App\Http\Controllers\Comments\StoreCommentController;
use App\Http\Controllers\Comments\UpdateCommentController;

Route::prefix('comments')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {
        Route::post('store', StoreCommentController::class)
            ->name('comments.store');

        Route::prefix('{comment:id}')->group(function () {
            Route::post('update', UpdateCommentController::class)
                ->name('comments.update');

            Route::post('delete', DeleteCommentController::class)
                ->name('comments.delete');
        });
    });
});
