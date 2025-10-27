<?php

use App\Http\Controllers\Partners\Details\ShowPartnerController;
use App\Http\Controllers\Partners\Details\ShowPartnerProjectsController;
use App\Http\Controllers\Partners\Details\ShowPartnerRelationshipsController;
use App\Http\Controllers\Partners\Details\ShowPartnerStatisticsController;
use App\Http\Controllers\Partners\Details\ShowPartnerUsersController;
use App\Http\Controllers\Partners\IndexPartnersController;

Route::prefix('partenaires')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin|partner'])->group(function () {
        Route::get('', IndexPartnersController::class)
            ->name('partners.index');

        Route::prefix('{partner:slug}')->group(function () {
            Route::get('', ShowPartnerController::class)
                ->name('partners.show');

            Route::get('projets', ShowPartnerProjectsController::class)
                ->name('partners.show.projects');

            Route::get('statistiques', ShowPartnerStatisticsController::class)
                ->name('partners.show.statistics');

            Route::get('users', ShowPartnerUsersController::class)
                ->name('partners.show.users');

            Route::get('relations', ShowPartnerRelationshipsController::class)
                ->name('partners.show.relationships');
        });
    });
});
