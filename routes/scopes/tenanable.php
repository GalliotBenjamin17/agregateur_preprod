<?php

use App\Http\Controllers\Api\Donations\RedirectAuthPaymentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Interface\DashboardController;
use App\Http\Controllers\Interface\Donations\ChooseDonationTypeController;
use App\Http\Controllers\Interface\Donations\CreateDonationFromUrlController;
use App\Http\Controllers\Interface\Donations\Details\ShowConfirmationController;
use App\Http\Controllers\Interface\Donations\IndexDonationsController;
use App\Http\Controllers\Interface\Faq\IndexFaqController;
use App\Http\Controllers\Interface\Profile\ShowProfileController;
use App\Http\Controllers\Interface\Profile\ShowProfileNotificationController;
use App\Http\Controllers\Interface\Profile\ShowProfileRgpdController;
use App\Http\Controllers\Interface\Projects\ShowProjectController;
use App\Http\Controllers\Interface\Resources\IndexResourcesController;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::domain('{tenant:domain}.'.config('app.displayed_url'))->group(function () {

    Route::prefix('inscription')->group(function () {
        Route::get('', [RegisteredUserController::class, 'create'])
            ->name('tenant.register');

        Route::post('', [RegisteredUserController::class, 'store'])
            ->middleware(ProtectAgainstSpam::class);
    });

    Route::middleware(['auth'])->group(function () {

        Route::get('accueil/{organization:slug?}', DashboardController::class)
            ->name('tenant.dashboard');

        Route::prefix('mes-contributions')->group(function () {

            Route::get('choose-type', ChooseDonationTypeController::class)
                ->name('tenant.donations.choose-type');

            Route::get('create', CreateDonationFromUrlController::class)
                ->name('tenant.donations.create');

            Route::get('redirect/donation', RedirectAuthPaymentController::class)
                ->name('api.donation.redirect-auth');

            Route::prefix('{donation}')->group(function () {

                Route::get('confirmation', ShowConfirmationController::class)
                    ->name('tenant.donations.confirmation');

            });

            Route::get('{organization:slug?}', IndexDonationsController::class)
                ->name('tenant.donations.index');

        });

        Route::prefix('mes-projets')->group(function () {

            Route::prefix('{project:slug}')->group(function () {

                Route::get('{organization:slug?}', ShowProjectController::class)
                    ->name('tenant.projects.show');

            });

        });

        Route::prefix('mes-ressources')->group(function () {

            Route::get('{organization:slug?}', IndexResourcesController::class)
                ->name('tenant.resources.index');

        });

        Route::prefix('faq')->group(function () {

            Route::get('{organization:slug?}', IndexFaqController::class)
                ->name('tenant.faq.index');

        });

        Route::prefix('profil')->group(function () {

            Route::get('informations/{organization:slug?}', ShowProfileController::class)
                ->name('tenant.profile.details');

            Route::get('notifications', ShowProfileNotificationController::class)
                ->name('tenant.profile.notifications');

            Route::get('rgpd', ShowProfileRgpdController::class)
                ->name('tenant.profile.rgpd');

        });
    });
});
