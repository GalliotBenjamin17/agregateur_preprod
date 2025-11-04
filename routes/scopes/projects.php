<?php

use App\Http\Controllers\Projects\DeleteProjectController;
use App\Http\Controllers\Projects\Details\ShowProjectContributorsController;
use App\Http\Controllers\Projects\Details\ShowProjectController;
use App\Http\Controllers\Projects\Details\ShowProjectCostController;
use App\Http\Controllers\Projects\Details\ShowProjectDetailsController;
use App\Http\Controllers\Projects\Details\ShowProjectDonationsController;
use App\Http\Controllers\Projects\Details\ShowProjectFinancialExportsController;
use App\Http\Controllers\Projects\Details\ShowProjectGoalsController;
use App\Http\Controllers\Projects\Details\ShowProjectMapController;
use App\Http\Controllers\Projects\Details\ShowProjectMethodInformationsController;
use App\Http\Controllers\Projects\Details\ShowProjectNewsController;
use App\Http\Controllers\Projects\Details\ShowProjectPartnersController;
use App\Http\Controllers\Projects\Details\ShowProjectPartnersDetailsController;
use App\Http\Controllers\Projects\ExportProjectsController;
use App\Http\Controllers\Projects\ExportSubProjectsController;
use App\Http\Controllers\Projects\IndexProjectsController;
use App\Http\Controllers\Projects\StoreProjectController;
use App\Http\Controllers\Projects\Updates\UpdateNextCertificationStateController;
use App\Http\Controllers\Projects\Updates\UpdateProjectAuditorController;
use App\Http\Controllers\Projects\Updates\UpdateProjectCoordinatesController;
use App\Http\Controllers\Projects\Updates\UpdateProjectReferentController;

Route::prefix('projets')->group(function () {
    Route::middleware(['auth', adminDashboardMiddleware()])->group(function () {
        Route::get('', IndexProjectsController::class)
            ->name('projects.index');

        Route::post('store', StoreProjectController::class)
            ->name('projects.store');

        Route::prefix('exports')->group(function () {
            Route::post('projets', ExportProjectsController::class)
                ->name('projects.export');

            Route::post('sous-projets', ExportSubProjectsController::class)
                ->name('projects.export.sub-projects');
        });

        Route::prefix('{project:slug}')->group(function () {
            Route::get('', ShowProjectController::class)
                ->name('projects.show');

            Route::get('details', ShowProjectDetailsController::class)
                ->name('projects.show.details');

            Route::get('carte', ShowProjectMapController::class)
                ->name('projects.show.map');

            // Renamed path from 'goals' to 'subproject' while keeping the same route name
            Route::get('subproject', ShowProjectGoalsController::class)
                ->name('projects.show.goals');
            // Backward-compatible redirect from old path
            Route::redirect('goals', 'subproject');

            Route::get('donations', ShowProjectDonationsController::class)
                ->name('projects.show.donations');

            Route::get('method-informations', ShowProjectMethodInformationsController::class)
                ->name('projects.show.methods-informations');

            Route::get('news', ShowProjectNewsController::class)
                ->name('projects.show.news');

            Route::get('contributors', ShowProjectContributorsController::class)
                ->name('projects.show.contributors');

            Route::get('financial-exports', ShowProjectFinancialExportsController::class)
                ->name('projects.show.financial-exports');

            Route::prefix('partenaires')->group(function () {

                Route::get('', ShowProjectPartnersController::class)
                    ->name('projects.show.partners');

                Route::get('{partnerProject}', ShowProjectPartnersDetailsController::class)
                    ->name('projects.show.partners.details');

            });

            Route::get('costs', ShowProjectCostController::class)
                ->name('projects.show.costs');

            Route::post('delete', DeleteProjectController::class)
                ->name('projects.delete');

            Route::prefix('update')->group(function () {

                Route::post('auditor', UpdateProjectAuditorController::class)
                    ->name('projects.update.auditor');

                Route::post('referent', UpdateProjectReferentController::class)
                    ->name('projects.update.referent');

                Route::post('coordinates', UpdateProjectCoordinatesController::class)
                    ->name('projects.update.coordinates');

                Route::post('next-certification-state', UpdateNextCertificationStateController::class)
                    ->name('projects.update.next-certification-state');

            });
        });
    });
});
