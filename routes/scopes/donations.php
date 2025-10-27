<?php

use App\Http\Controllers\Donations\Details\ExportDonationCertificateController;
use App\Http\Controllers\Donations\Details\ShowDonationController;
use App\Http\Controllers\Donations\Details\ShowDonationDetailsController;
use App\Http\Controllers\Donations\Details\ShowDonationsSplitController;
use App\Http\Controllers\Donations\ExportDonationsController;
use App\Http\Controllers\Donations\ExportDonationsSplitsController;
use App\Http\Controllers\Donations\IndexDonationsController;
use App\Http\Controllers\Donations\StoreDonationController;

Route::prefix('contributions')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {
        Route::get('', IndexDonationsController::class)
            ->name('donations.index');

        Route::post('store', StoreDonationController::class)
            ->name('donations.store');

        Route::post('export', ExportDonationsController::class)
            ->name('donations.export');

        Route::post('export-splits', ExportDonationsSplitsController::class)
            ->name('donations.export-splits');

        Route::prefix('{donation:id}')->group(function () {
            Route::get('', ShowDonationController::class)
                ->name('donations.show');

            Route::get('split', ShowDonationsSplitController::class)
                ->name('donations.show.split');

            Route::get('details', ShowDonationDetailsController::class)
                ->name('donations.show.details');

            Route::post('certificate', ExportDonationCertificateController::class)
                ->name('donations.download.certificate');
        });
    });
});
