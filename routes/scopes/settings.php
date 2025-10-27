<?php

use App\Http\Controllers\Settings\IndexAdminsController;
use App\Http\Controllers\Settings\IndexBadgesSettingsController;
use App\Http\Controllers\Settings\IndexBrandGuidelinesSettingsController;
use App\Http\Controllers\Settings\IndexCertificationsController;
use App\Http\Controllers\Settings\IndexCustomFieldsSettingsController;
use App\Http\Controllers\Settings\IndexEmailsSettingsController;
use App\Http\Controllers\Settings\IndexLogsSettingsController;
use App\Http\Controllers\Settings\IndexMethodFormsController;
use App\Http\Controllers\Settings\IndexOrganizationTypesController;
use App\Http\Controllers\Settings\IndexSegmentationsController;
use App\Http\Controllers\Settings\IndexTenantsSettingsController;
use App\Http\Controllers\Settings\IndexTerminalsController;
use App\Http\Controllers\Settings\IndexVariablesSettingsController;
use App\Http\Controllers\Settings\ShowMethodFormController;
use App\Http\Controllers\Settings\ShowMethodFormGroupController;
use App\Http\Controllers\Settings\ShowTenantSettingsController;

Route::prefix('settings')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {
        Route::get('', IndexBrandGuidelinesSettingsController::class)
            ->name('settings.index.brand-guidelines');

        Route::get('variables', IndexVariablesSettingsController::class)
            ->name('settings.index.variables');

        Route::get('admins', IndexAdminsController::class)
            ->name('settings.index.admins');

        Route::get('emails', IndexEmailsSettingsController::class)
            ->name('settings.index.emails');

        Route::get('logs', IndexLogsSettingsController::class)
            ->name('settings.index.logs');

        Route::get('custom-fields', IndexCustomFieldsSettingsController::class)
            ->name('settings.index.custom-fields');

        Route::get('organization-types', IndexOrganizationTypesController::class)
            ->name('settings.index.organization-types');

        Route::get('terminals', IndexTerminalsController::class)
            ->name('settings.index.terminals');

        Route::get('segmentations', IndexSegmentationsController::class)
            ->name('settings.index.segmentations');

        Route::get('certifications', IndexCertificationsController::class)
            ->name('settings.index.certifications');

        Route::prefix('methodes')->group(function () {
            Route::get('', IndexMethodFormsController::class)
                ->name('settings.method-forms.index');

            Route::prefix('{methodFormGroup:slug}')->group(function () {
                Route::get('', ShowMethodFormGroupController::class)
                    ->name('settings.method-form-groups.show');

                Route::prefix('{methodForm}')->group(function () {
                    Route::get('', ShowMethodFormController::class)
                        ->name('settings.method-form-groups.method-form.show');

                });
            });

        });

        Route::prefix('antennes-locales')->group(function () {
            Route::get('', IndexTenantsSettingsController::class)
                ->name('settings.index.tenants');

            Route::get('{tenant:slug}', ShowTenantSettingsController::class)
                ->name('settings.show.tenants');
        });

        Route::prefix('badges')->group(function () {

            Route::get('', IndexBadgesSettingsController::class)
                ->name('settings.badges.index');

        });

    });
});
