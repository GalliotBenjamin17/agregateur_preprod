<?php

use App\Http\Controllers\Transactions\IndexTransactionsController;
use App\Http\Controllers\Transactions\Webhooks\FailedTransactionController;
use App\Http\Controllers\Transactions\Webhooks\SuccessTransactionController;

Route::prefix('transactions')->group(function () {
    Route::middleware(['auth', 'role:admin|local_admin'])->group(function () {
        Route::get('', IndexTransactionsController::class)
            ->name('transactions.index');
    });

    Route::prefix('webhooks')->group(function () {
        Route::get('success', SuccessTransactionController::class)
            ->name('transactions.webhooks.success');

        Route::get('failed', FailedTransactionController::class)
            ->name('transactions.webhooks.failed');
    });
});
