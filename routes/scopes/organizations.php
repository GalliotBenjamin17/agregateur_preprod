<?php

use App\Http\Controllers\Organizations\Api\GetOrganizationsController;
use App\Http\Controllers\Organizations\Details\ShowDetailsOrganizationController;
use App\Http\Controllers\Organizations\Details\ShowOrganizationController;
use App\Http\Controllers\Organizations\Details\ShowOrganizationDonationsController;
use App\Http\Controllers\Organizations\Details\ShowOrganizationUsersController;
use App\Http\Controllers\Organizations\Details\ShowProjectsOrganizationController;
use App\Http\Controllers\Organizations\IndexOrganizationsController;
use App\Http\Controllers\Organizations\StoreOrganizationController;
use App\Http\Controllers\Organizations\UpdateUserLinkController;
use App\Http\Controllers\Organizations\UpdateUserManagerController;
use App\Http\Controllers\Organizations\UpdateUsersController;
use App\Http\Middleware\Features\CanSeeOrganizationMiddleware;

Route::prefix('organisations')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin|member'])->group(function () {
        Route::get('', IndexOrganizationsController::class)
            ->name('organizations.index');

        Route::post('store', StoreOrganizationController::class)
            ->middleware('role:admin|local_admin')
            ->name('organizations.store');

        Route::prefix('{organization:slug}')->middleware(CanSeeOrganizationMiddleware::class)->group(function () {
            Route::get('', ShowOrganizationController::class)
                ->name('organizations.show');

            Route::get('details', ShowDetailsOrganizationController::class)
                ->name('organizations.show.details');

            Route::get('projets', ShowProjectsOrganizationController::class)
                ->name('organizations.show.projects');

            Route::get('contributions', ShowOrganizationDonationsController::class)
                ->name('organizations.show.donations');

            Route::get('utilisateurs', ShowOrganizationUsersController::class)
                ->name('organizations.show.users');

            Route::post('update-users', UpdateUsersController::class)
                ->name('organizations.update.users');

            Route::post('update-manager', UpdateUserManagerController::class)
                ->name('organizations.update.manager');

            Route::post('update-user-link/{user:id}', UpdateUserLinkController::class)
                ->name('organizations.update.user-link');
        });

        Route::prefix('api')->group(function () {
            Route::get('search', GetOrganizationsController::class)
                ->name('organizations.api.search');
        });
    });
});
