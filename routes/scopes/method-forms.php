<?php

use App\Http\Controllers\MethodFormGroups\StoreMethodFormGroupsController;
use App\Http\Controllers\MethodForms\StoreMethodFormsController;

Route::prefix('method-forms-group')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {
        Route::post('store', StoreMethodFormGroupsController::class)
            ->name('method-form-groups.store');
    });
});

Route::prefix('method-forms')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {
        Route::post('{methodFormGroup:slug}/store', StoreMethodFormsController::class)
            ->name('method-forms.store');
    });
});
