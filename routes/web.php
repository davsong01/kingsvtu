<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\TransactionController;

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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('customer/{slug}', [TransactionController::class, 'showProductsPage'])->name('open.transaction.page');
    Route::get('customer-get-variations/{product}', [VariationController::class, 'getCustomerVariations'])->name('get.customer.variations');
    Route::post('customer-initialize-transaction', [TransactionController::class, 'initializeTransaction'])->name('initialize.transaction');
    
    
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::middleware(['auth', 'verified','admin'])->prefix('admin')->group(function () {
    Route::resource('product', ProductController::class);
    Route::resource('api', APIController::class);
    Route::resource('category', CategoryController::class);

    Route::get('pull-variations/{product}', [VariationController::class, 'pullVariations'])->name('variations.pull');
    Route::post('update-variations/{product}', [VariationController::class, 'updateVariations'])->name('variations.update');
    
    
    
});

require __DIR__.'/auth.php';
