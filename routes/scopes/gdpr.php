<?php

use App\Http\Controllers\Gdpr\AccountDeletionController;
use App\Http\Controllers\Gdpr\AccountDownloadController;
use App\Http\Controllers\Gdpr\RedirectToCorrectPageController;
use App\Http\Controllers\Gdpr\See\ShowCodeController;
use App\Http\Controllers\Gdpr\ShowSeeController;
use App\Http\Controllers\Gdpr\StoreRequestController;
use App\Http\Controllers\Gdpr\Unsubscribe\StoreUnsubscribeController;
use Illuminate\Support\Facades\Route;

Route::prefix('rgpd')->group(function () {
    Route::prefix('hub')->group(function () {
        Route::view('', 'gdpr.index-hub')
            ->name('gdpr.hub.index');

        Route::prefix('voir')->group(function () {
            Route::view('', 'gdpr.see.index')
                ->name('gdpr.hub.see.index');

            Route::get('code', ShowCodeController::class)
                ->name('gdpr.hub.see.code');

            Route::get('{gdprRequest:id}', ShowSeeController::class)
                ->whereUuid('id')
                ->name('gdpr.hub.see.show');
        });

        Route::post('ask-request', StoreRequestController::class)
            ->name('gdpr.hub.ask-request');

        Route::post('redirect-to-request', RedirectToCorrectPageController::class)
            ->name('gdpr.hub.redirect-to-request');
    });

    Route::prefix('compte')->group(function () {
        Route::view('suppression', 'gdpr.show-delete')
            ->name('gdpr.account.confirm-delete');

        Route::delete('suppression', AccountDeletionController::class)
            ->name('gdpr.account.delete');

        Route::middleware('auth')->group(function () {
            Route::view('donnees', 'gdpr.show-download')
                ->name('gdpr.account.confirm-download');

            Route::post('donnees', AccountDownloadController::class)
                ->name('gdpr.account.download');
        });
    });

    Route::prefix('se-desinscrire')->group(function () {
        Route::view('', 'gdpr.show-unsubscribe')
            ->name('gdpr.account.unsubscribe');

        Route::post('store', StoreUnsubscribeController::class)
            ->name('gdpr.account.unsubscribe.store');
    });
});
