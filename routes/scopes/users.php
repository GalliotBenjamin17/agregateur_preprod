<?php

use App\Http\Controllers\Users\Api\GetUsersSearchController;
use App\Http\Controllers\Users\DeleteUserController;
use App\Http\Controllers\Users\Details\ShowUserController;
use App\Http\Controllers\Users\Details\ShowUserDetailsController;
use App\Http\Controllers\Users\Details\ShowUserDonationsController;
use App\Http\Controllers\Users\ExportUsersController;
use App\Http\Controllers\Users\IndexUsersController;
use App\Http\Controllers\Users\StoreUserController;
use App\Http\Controllers\Users\UpdateOrganizationsController;
use App\Http\Controllers\Users\UpdateTagsController;

Route::prefix('acteurs')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('', IndexUsersController::class)
            ->middleware('role:admin|local_admin')
            ->name('users.index');

        Route::post('store', StoreUserController::class)
            ->middleware('role:admin|local_admin|member')
            ->name('users.store');

        Route::post('export', ExportUsersController::class)
            ->middleware('role:admin|local_admin')
            ->name('users.export');

        Route::prefix('{user:slug}')
            ->middleware('role:admin|local_admin')
            ->group(function () {
                Route::get('', ShowUserController::class)
                    ->name('users.show');

                Route::get('details', ShowUserDetailsController::class)
                    ->name('users.show.details');

                Route::get('contributions', ShowUserDonationsController::class)
                    ->name('users.show.donations');

                Route::post('update/organizations', UpdateOrganizationsController::class)
                    ->name('users.update.organizations');

                Route::post('tags', UpdateTagsController::class)
                    ->name('users.update.tags');

                Route::delete('delete', DeleteUserController::class)
                    ->name('users.delete');
            });

        Route::prefix('api')->group(function () {
            Route::get('search', GetUsersSearchController::class)
                ->name('users.api.search');
        });
    });
});
