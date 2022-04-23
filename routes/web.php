<?php

use App\Http\Controllers\AuditLogsController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCommonController;
use App\Http\Controllers\ExpenseCommonHistoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseHistoryController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\InvoiceItemsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\Reference\ItemCategoryController;
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

// Images
Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');

Route::get('/file/{path}', [ImagesController::class, 'path'])
    ->where('path', '.*')
    ->name('image');

Route::middleware('auth')->group(function () {

    // Dashboard

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('auth');

    Route::get('auth/{user}', [UsersController::class, 'auth'])->name('auth');
    Route::put('auth/{user}', [UsersController::class, 'password']);

    Route::middleware('role:Супер Администратор')->group(function () {

        // Logs
        // Logs pages
        Route::prefix('log')->name('log.')->group(function () {
            // Route::resource('system', SystemLogsController::class)->only(['index', 'destroy']);
            Route::get('', [AuditLogsController::class, 'index']);
        });
    });

    Route::middleware('role:Супер Администратор,Администратор')->group(function () {

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
    });

    // Organizations
    Route::prefix('organizations')->middleware('role:Супер Администратор,Администратор,Бухгалтер')->group(function () {
        Route::get('', [OrganizationsController::class, 'index'])->name('organizations');
    });

    Route::prefix('organizations')->middleware('role:Супер Администратор,Администратор')->name('organizations')->controller(OrganizationsController::class)->group(function () {
        Route::get('create', 'create')->name('.create');
        Route::post('', 'store')->name('.store');
        Route::get('{organization}/edit', 'edit')->name('.edit');
        Route::put('{organization}', 'update')->name('.update');
        Route::delete('{organization}', 'destroy')->name('.destroy');
        Route::put('{organization}/restore', 'restore')->name('.restore');
    });

    Route::middleware('role:Супер Администратор,Кассир')->group(function () {

        // Reports
        Route::prefix('reports')->name('reports')->controller(ReportsController::class)->group(function () {
            Route::get('common', 'common');
            Route::get('', 'index');
            Route::get('{organization}/{supplier}/all', 'all')
                ->name('.all');
            Route::get('{organization}/{supplier}/pay', 'pay')
                ->name('.pay');
            Route::get('{organization}/{supplier}/not_pay', 'notPay')
                ->name('.not_pay');
            Route::get('{organization}/{supplier}/{invoice}/items', 'invoiceItem')
                ->name('.invoice.item');

            Route::get('items', 'items')
                ->name('.items');
            Route::get('items/{organization}/{item}/supplier', 'itemSupplier')
                ->name('.items.supplier');

            Route::get('expense', 'expense')
                ->name('.expense');

            Route::get('export-index', 'exportIndex')
                ->name('.export-index');
            Route::get('export-item', 'exportItem')
                ->name('.export-item');
            Route::get('export-expense', 'exportExpense')
                ->name('.export-expense');
        });
    });

    Route::middleware('role:Супер Администратор,Бухгалтер')->group(function () {

        // Expense
        Route::prefix('organizations/{organization}/expense')->name('expense')->controller(ExpenseController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::put('{expense}', 'update')->name('.update');
            Route::get('create', 'create')->name('.create');
        });

        // ExpenseHistory
        Route::prefix('organizations/expenses/{expense}')->name('expenses-history')->controller(ExpenseHistoryController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::delete('{expense_history}', 'destroy')->name('.delete');
            Route::get('create', 'create')->name('.create');
        });

        // ExpenseCommon
        Route::prefix('expense-common')->name('expense-common')->controller(ExpenseCommonController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::put('{expense}', 'update')->name('.update');
            Route::get('create', 'create')->name('.create');
        });

        // ExpenseCommonHistory
        Route::prefix('expense-common/{expense}')->name('expense-common-history')->controller(ExpenseCommonHistoryController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::delete('{expense_history}', 'destroy')->name('.delete');
            Route::get('create', 'create')->name('.create');
        });

        // Invoice
        Route::prefix('organizations/{organization}/invoices')->name('invoices')->controller(InvoicesController::class)->group(function () {
            Route::get('', 'index');
            Route::post('', 'store')->name('.store');
            Route::put('{invoice}', 'update')->name('.update');
            Route::get('create', 'create')->name('.create');
            Route::delete('{invoice}', 'destroy')->name('.destroy')
                ->middleware('role:Супер Администратор');
        });

        // InvoiceItem
        Route::prefix('invoices/{invoice}/invoice-items')->name('invoice-items')->controller(InvoiceItemsController::class)->group(function () {
            Route::get('', 'index');
            Route::post('confirm', 'confirm')->name('.confirm');
            Route::put('', 'update')->name('.update');
            Route::delete('', 'delete')->name('.delete');
            Route::post('/restore', 'restore')->name('.restore');
            Route::post('{item}', 'store')->name('.store');
            Route::get('pay', 'pay')->name('.pay');
        });

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

            // Item
            Route::prefix('items')->name('.items')->controller(ItemController::class)->group(function () {
                Route::get('', 'index');
                Route::post('', 'store')->name('.store');
                Route::get('{item}/edit', 'edit')->name('.edit');
                Route::put('{item}', 'update')->name('.update');
                Route::delete('{item}', 'destroy')->name('.destroy');
                Route::put('{item}/restore', 'restore')->name('.restore');
            });

            // ItemCategory
            Route::prefix('item-categories')->name('.items-categories')->controller(ItemCategoryController::class)->group(function () {
                Route::get('', 'index');
                Route::post('', 'store')->name('.store');
                Route::get('{item_category}/edit', 'edit')->name('.edit');
                Route::put('{item_category}', 'update')->name('.update');
                Route::delete('{item_category}', 'destroy')->name('.destroy');
                Route::put('{item_category}/restore', 'restore')->name('.restore');
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
});
