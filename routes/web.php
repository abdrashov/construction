<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\InvoiceItemsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\Reference\AcceptedController;
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
        Route::get('{user}/edit', 'edit')->name('.edit');
        Route::put('{user}', 'update')->name('.update');
    });

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
        Route::get('{invoice}', 'show')->name('.show');
    });

    // InvoiceItem

    Route::prefix('invoices/{invoice}/invoice-items')->name('invoice-items')->controller(InvoiceItemsController::class)->group(function () {
        Route::get('', 'index');
        Route::post('', 'store')->name('.store');
        Route::get('{invoice_item}', 'show')->name('.show');
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

        Route::prefix('suppliers')->name('suppliers')->controller(SuppliersController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::get('{invoice_item}', 'show')->name('.show');
        });

        // Suppliers

        Route::prefix('accepted')->name('accepted')->controller(AcceptedController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::get('{invoice_item}', 'show')->name('.show');
        });
    });
});
