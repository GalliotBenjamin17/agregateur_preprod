<?php

use App\Http\Controllers\Api\Donations\CreateFromWebsiteController;
use App\Http\Controllers\Api\Donations\StorePaymentFromTerminalController;
use App\Http\Controllers\Api\News\GetNewsController;
use App\Http\Controllers\Api\News\GetProjectNewController;
use App\Http\Controllers\Api\Organizations\GetOrganizationsController;
use App\Http\Controllers\Api\Projects\GetProjectController;
use App\Http\Controllers\Api\Projects\GetProjectsController;
use App\Http\Controllers\Api\Tenants\GetTenantController;
use App\Http\Controllers\Api\Users\GetUsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
     * Projects routes
     */
Route::prefix('projects')->group(function () {
    Route::get('', GetProjectsController::class)
        ->name('api.projects');

    Route::get('{project:id}', GetProjectController::class)
        ->name('api.project');
});

/*
 * Users routes
 */
Route::prefix('users')->group(function () {
    Route::get('', GetUsersController::class)
        ->name('api.users');
});

/*
 * Organizations routes
 */
Route::prefix('organizations')->group(function () {
    Route::get('', GetOrganizationsController::class)
        ->name('api.organizations');
});

/*
* Donations routes
*/
Route::prefix('donations')->group(function () {

    Route::match(['GET', 'POST'], 'store', CreateFromWebsiteController::class)
        ->name('api.donation.store');

});

Route::prefix('borne')->group(function () {
    Route::post('create-paiement/{terminal}/{project}', StorePaymentFromTerminalController::class)
        ->name('api.terminal.donation.store');
});

/*
* News routes
*/

Route::prefix('news')->group(function () {

    Route::get('', GetNewsController::class)
        ->name('api.news');

    Route::prefix('{project:id}')->group(function () {
        Route::get('', GetProjectNewController::class)
            ->name('api.projects.news');
    });

});

/*
* Tenant route
*/

Route::prefix('tenants')->group(function () {

    Route::get('{tenant:id}', GetTenantController::class)
        ->name('api.tenant.details');

});
