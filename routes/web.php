<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('redirect', RedirectController::class)
    ->middleware(['auth'])
    ->name('redirect');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', adminDashboardMiddleware()])
    ->name('dashboard');

foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__.'/scopes') as $scope) {
    require $scope->getPathname();
}

Route::view('rapport-annuel', 'pdfs.annual-report');

require __DIR__.'/auth.php';

Route::get('local/temp/{path}', function (string $path) {
    return Storage::disk('local')->download($path);
})->name('local.temp');


