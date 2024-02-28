<?php

use App\Http\Controllers\BlackListController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CustomerLevelController;
use App\Http\Controllers\PaymentGatewayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::post('log-p-callback/{provider}', [PaymentController::class, 'logPaymentResponse'])->name('log.payment.response');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/reset-transaction-pin', [DashboardController::class, 'resetTransactionPin'])->name('customer.reset.pin');
    Route::post('/process-transaction-pin-reset', [DashboardController::class, 'processResetTransactionPin'])->name('process.transaction.pin.reset');
    Route::get('confirm_reset_pin', [DashboardController::class, 'resetPin2']);
    Route::post('reset_pin_final', [DashboardController::class, 'finalProcessPin'])->name('final.pin.reset');
    // Route::post('change-pin', [HomeController::class, 'processResetPin'])->name('pin.process.reset');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('customer/{slug}', [TransactionController::class, 'showProductsPage'])->name('open.transaction.page');
    Route::get('customer-get-variations/{product}', [VariationController::class, 'getCustomerVariations'])->name('get.customer.variations');
    Route::post('customer-initialize-transaction', [TransactionController::class, 'initializeTransaction'])->name('initialize.transaction');
    Route::post('customer-verify', [TransactionController::class, 'verify'])->name('verify.unique.element');
    Route::get('customer-transactions', [TransactionController::class, 'customerTransactionHistory'])->name('customer.transaction.history');
    Route::get('customer-transaction_status/{transaction_id}', [TransactionController::class, 'transactionStatus'])->name('transaction.status');
    Route::get('customer-level-upgrade', [DashboardController::class, 'showUpgradeForm'])->name('customer.level.upgrade');
    Route::get('customer-load-wllet', [DashboardController::class, 'showLoadWalletPge'])->name('customer.load.wallet');

    Route::post('level-upgrade', [DashboardController::class, 'upgradeAccount'])->name('customer.level.upgrade.process');

    Route::get('download-transaction-receipt/{transaction_id}', [TransactionController::class, 'transactionReceipt'])->name('transaction.receipt.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::resource('product', ProductController::class);
    Route::get('duplicate-product/{product}', [ProductController::class, 'duplicateProduct'])->name('duplicate.product');
    Route::resource('api', APIController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('customer-blacklist', BlackListController::class);
    Route::get('black-list-status', [BlackListController::class, 'status']);

    // transactions route
    Route::get('transactions', [TransactionController::class, 'transView'])->name('admin.trans');

    Route::get('customers/{status?}', [CustomerController::class, 'customers'])->name('customers');
    Route::get('customers-active/{status}', [CustomerController::class, 'customers'])->name('customers.active');
    Route::get('customers-suspended/{status}', [CustomerController::class, 'customers'])->name('customers.suspended');
    Route::get('customer/edit/{id}', [CustomerController::class, 'singleCustomer'])->name('customers.edit');
    Route::post('customer/update/{id}', [CustomerController::class, 'updateCustomer'])->name('customers.update');
    Route::resource('customerlevel', CustomerLevelController::class);

    Route::get('pull-variations/{product}', [VariationController::class, 'pullVariations'])->name('variations.pull');
    Route::post('update-variations/{product}', [VariationController::class, 'updateVariations'])->name('variations.update');

    Route::controller(AdminController::class)->group(function () {
        Route::get('admins', 'index')->name('admins');
        Route::get('admin/new', 'create')->name('newAdmin');
        Route::post('admin/save', 'store')->name('adminSave');
        Route::get('admin/view', 'view')->name('viewAdmin');
        Route::post('admin/update', 'update')->name('updateAdmin');
    });

    Route::get('settings-update', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::post('settings-update', [SettingsController::class, 'update'])->name('settings.update');

    Route::resource('paymentgateway', PaymentGatewayController::class);
});

require __DIR__ . '/auth.php';
