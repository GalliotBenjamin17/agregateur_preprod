<?php

use App\Http\Controllers\Files\DeleteFileController;
use App\Http\Controllers\Files\Details\DownloadFileController;
use App\Http\Controllers\Files\Details\PreviewFileController;
use App\Http\Controllers\Files\Details\ShowFileController;
use App\Http\Controllers\Files\IndexFilesController;
use App\Http\Controllers\Files\StoreFileController;

Route::prefix('fichiers')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('', IndexFilesController::class)
            ->middleware('role:admin|local_admin')
            ->name('files.index');

        Route::post('store', StoreFileController::class)
            ->middleware('role:admin|local_admin|auditor')
            ->name('files.store');

        Route::prefix('{file:slug}')->group(function () {
            Route::get('', ShowFileController::class)
                ->middleware('role:admin|local_admin|auditor')
                ->name('files.show');

            Route::delete('delete', DeleteFileController::class)
                ->middleware('role:admin|local_admin|auditor')
                ->name('files.delete');

            Route::get('preview', PreviewFileController::class)
                ->name('files.show.preview');

            Route::post('download', DownloadFileController::class)
                ->name('files.show.download');
        });
    });
});
