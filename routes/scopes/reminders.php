<?php

use App\Http\Controllers\Reminders\DeleteReminderController;
use App\Http\Controllers\Reminders\StoreReminderController;

Route::middleware(['auth', 'role:admin|local_admin'])->prefix('rappels')->group(function () {
    Route::post('', StoreReminderController::class)
        ->name('reminder.store');

    Route::delete('{reminder:id}', DeleteReminderController::class)
        ->whereUuid('id')
        ->name('reminder.delete');
});
