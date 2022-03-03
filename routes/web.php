<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\InvoiceItemsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\Reference\AcceptedController;
use App\Http\Controllers\Reference\ItemController;
use App\Http\Controllers\Reference\MeasurementsController;
use App\Http\Controllers\Reference\SuppliersController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Auth

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::middleware('auth')->group(function () {

    // Dashboard

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('auth');

    // Users

    Route::prefix('users')->name('users')->controller(UsersController::class)->group(function () {
        Route::get('', 'index');
        Route::get('create', 'create')->name('create');
        Route::post('', 'store')->name('store');
        Route::get('{user}/edit', 'edit')->name('.edit');
        Route::put('{user}', 'update')->name('.update');
        Route::delete('{user}', 'destroy')->name('destroy');
        Route::put('{user}/restore', 'restore')->name('restore');
    });

    Route::get('auth/{user}', [UsersController::class, 'auth']);

    // Organizations

    Route::prefix('organizations')->name('organizations')->controller(OrganizationsController::class)->group(function () {
        Route::get('', 'index');
        Route::get('create', 'create')->name('.create');
        Route::post('', 'store')->name('.store');
        Route::get('{organization}/edit', 'edit')->name('.edit');
        Route::put('{organization}', 'update')->name('.update');
        Route::delete('{organization}', 'destroy')->name('.destroy');
        Route::put('{organization}/restore', 'restore')->name('.restore');
    });

    // Invoice

    Route::prefix('organizations/{organization}/invoices')->name('invoices')->controller(InvoicesController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store')->name('.store');
        Route::put('{invoice}', 'update')->name('.update');
        Route::get('create', 'create')->name('.create');
    });
    // InvoiceItem

    Route::prefix('invoices/{invoice}/invoice-items')->name('invoice-items')->controller(InvoiceItemsController::class)->group(function () {
        Route::get('', 'index');
        Route::post('confirm', 'confirm')->name('.confirm');
        Route::put('', 'update')->name('.update');
        Route::delete('', 'delete')->name('.delete');
        Route::post('{item}', 'store')->name('.store');
    });

    // Reports

    Route::get('reports', [ReportsController::class, 'index'])
        ->name('reports');

    // Images

    Route::get('/img/{path}', [ImagesController::class, 'show'])
        ->where('path', '.*')
        ->name('image');

    // Reference

    Route::prefix('reference')->name('reference')->group(function () {

        // Suppliers

        Route::prefix('suppliers')->name('.suppliers')->controller(SuppliersController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::get('{supplier}/edit', 'edit')->name('.edit');
            Route::put('{supplier}', 'update')->name('.update');
            Route::delete('{supplier}', 'destroy')->name('.destroy');
            Route::put('{supplier}/restore', 'restore')->name('.restore');
        });

        // Accepted

        Route::prefix('accepted')->name('.accepted')->controller(AcceptedController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::get('{accepted}/edit', 'edit')->name('.edit');
            Route::put('{accepted}', 'update')->name('.update');
            Route::delete('{accepted}', 'destroy')->name('.destroy');
            Route::put('{accepted}/restore', 'restore')->name('.restore');
        });

        // Item

        Route::prefix('items')->name('.items')->controller(ItemController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::get('{item}/edit', 'edit')->name('.edit');
            Route::put('{item}', 'update')->name('.update');
            Route::delete('{item}', 'destroy')->name('.destroy');
            Route::put('{item}/restore', 'restore')->name('.restore');
        });

        // Measurement

        Route::prefix('measurements')->name('.measurements')->controller(MeasurementsController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::get('{measurement}/edit', 'edit')->name('.edit');
            Route::put('{measurement}', 'update')->name('.update');
            Route::delete('{measurement}', 'destroy')->name('.destroy');
            Route::put('{measurement}/restore', 'restore')->name('.restore');
        });
    });
});
