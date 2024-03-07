<?php

use App\Http\Controllers\AnnouncementController;
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
use App\Http\Controllers\KycDataController;
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

Route::post('log-p-callback/{provider}', [PaymentController::class, 'dumpCallback'])->name('log.payment.response');
Route::get('analyze-callback', [PaymentController::class, 'analyzeCallbackResponse'])->name('callback.analyze');

Route::middleware(['auth', 'verified', 'ipcheck'])->group(function () {
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
    Route::get('customer-transaction-report', [TransactionController::class, 'showTransactionReportPage'])->name('customer.transaction.report');
    Route::get('customer-level-upgrade', [DashboardController::class, 'showUpgradeForm'])->name('customer.level.upgrade');
    Route::get('customer-load-wllet', [DashboardController::class, 'showLoadWalletPge'])->name('customer.load.wallet');
    Route::post('process-customer-load-wllet', [PaymentController::class, 'redirectToUrl'])->name('process-customer-load-wllet');
    Route::get('payment-callback/{provider_id?}', [PaymentController::class, 'analyzePaymentResponse'])->name('payment-callback');
    Route::get('customer-update-kyc-info', [DashboardController::class, 'updateKycInfo'])->name('update.kyc.details');
    Route::post('customer-update-kyc-info', [DashboardController::class, 'processUpdateKycInfo'])->name('update.kyc.details.process');
    Route::get('get-lga-by-statename/{state}', [KycDataController::class, 'getLgaByStateName'])->name('kyc-get-lga-by-state');

    // Route::post('transaction-confirm/{provider}/{reference?}', [PaymentController::class, 'logPaymentResponse'])->name('log.payment.response');
    Route::post('level-upgrade', [DashboardController::class, 'upgradeAccount'])->name('customer.level.upgrade.process');
    Route::get('download-transaction-receipt/{transaction_id}', [TransactionController::class, 'transactionReceipt'])->name('transaction.receipt.download');
    Route::get('downlines/process/withdrawal', [DashboardController::class, 'downlinesWithdrawal'])->name('downlines.withdraw');
    Route::post('downlines/withdraw', [DashboardController::class, 'processWithdrawal'])->name('process.withdrawal');
    Route::get('downlines/{id?}', [DashboardController::class, 'downlines'])->name('downlines');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::middleware(['auth', 'verified', 'admin', 'ipcheck'])->prefix('admin')->group(function () {
    Route::resource('product', ProductController::class);
    Route::get('duplicate-product/{product}', [ProductController::class, 'duplicateProduct'])->name('duplicate.product');
    Route::resource('api', APIController::class);
    Route::get('api-balance/{api}', [APIController::class, 'getBalance'])->name('api.balance');

    Route::resource('category', CategoryController::class);
    Route::resource('customer-blacklist', BlackListController::class);
    Route::get('announcement/scroll', [AnnouncementController::class, 'scroll'])->name('announcement.scroll');
    Route::resource('announcement', AnnouncementController::class);
    Route::get('black-list-status', [BlackListController::class, 'status']);

    // transactions route
    Route::get('transactions', [TransactionController::class, 'transView'])->name('admin.trans');
    Route::get('wallet-transactions', [TransactionController::class, 'walletTransView'])->name('admin.walletlog');
    Route::get('admin-wallet-funding-log', [TransactionController::class, 'walletFundingLogView'])->name('admin.walletfundinglog');
    Route::get('admin-earninglog', [TransactionController::class, 'walletEarningView'])->name('admin.earninglog');
    Route::get('credit-customer', [TransactionController::class, 'creditCustomerPage'])->name('admin.credit.customer');
    Route::get('debit-customer', [TransactionController::class, 'debitCustomerPage'])->name('admin.debit.customer');
    Route::post('process-credit-debit', [TransactionController::class, 'processCreditDebit'])->name('admin.process.credit.debit');
    Route::post('verify-biller/{admin?}', [TransactionController::class, 'verify'])->name('admin.verifybiller');

    Route::get('single-transaction-view/{transaction}', [TransactionController::class, 'singleTransactionView'])->name('admin.single.transaction.view');
    Route::get('query-wallet/{transactionlog?}', [TransactionController::class, 'queryWallet'])->name('admin.query.wallet');
    Route::get('requery-transaction/{transactionlog?}', [TransactionController::class, 'requery'])->name('admin.requery.transaction');

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

    Route::get('verify-transaction/{reference}/{provider_id?}', [PaymentController::class, 'verifyPayment'])->name('transaction.verify');

    Route::resource('paymentgateway', PaymentGatewayController::class);
});

require __DIR__ . '/auth.php';
