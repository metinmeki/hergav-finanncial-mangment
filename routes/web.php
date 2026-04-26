<?php

use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\BailmentController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;

// Language switch
Route::get('/lang/{lang}', function($lang) {
    $validLang = in_array($lang, ['en', 'ar']) ? $lang : 'en';
    session(['lang' => $validLang]);
    \Illuminate\Support\Facades\App::setLocale($validLang);
    return redirect()->back()->with('lang_changed', true);
})->name('lang.switch');

// Auth routes
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Client Portal — redirect old URL to /login
Route::get('/portal/login', fn() => redirect()->route('login'))->name('portal.login');
Route::post('/portal/login', [ClientPortalController::class, 'login'])->name('portal.login.post');
Route::get('/portal/dashboard', [ClientPortalController::class, 'dashboard'])->name('portal.dashboard');
Route::post('/portal/logout', [ClientPortalController::class, 'logout'])->name('portal.logout');
Route::post('/portal/mark-seen', [ClientPortalController::class, 'markSeen'])->name('portal.mark-seen');

// Protected routes (staff only)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::post('/clients/{id}/enable-login', [ClientController::class, 'enableLogin'])->name('clients.enable-login');
    Route::post('/clients/{id}/disable-login', [ClientController::class, 'disableLogin'])->name('clients.disable-login');
    Route::get('/debt', [ClientController::class, 'debt'])->name('clients.debt');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('/transactions/transfer', [TransactionController::class, 'transfer'])->name('transactions.transfer');
    Route::get('/transactions/daily', [TransactionController::class, 'dailyReport'])->name('transactions.daily');

    // Bailment
    Route::get('/bailment', [BailmentController::class, 'index'])->name('bailment.index');
    Route::post('/bailment', [BailmentController::class, 'store'])->name('bailment.store');
    Route::get('/bailment/client/{id}', [BailmentController::class, 'clientBailment'])->name('bailment.client');

    // Receipts
    Route::get('/receipts/transaction/{id}', [ReceiptController::class, 'transaction'])->name('receipts.transaction');
    Route::get('/receipts/transfer/{id}', [ReceiptController::class, 'transfer'])->name('receipts.transfer');
    Route::get('/receipts/bailment/{id}', [ReceiptController::class, 'bailment'])->name('receipts.bailment');
    Route::get('/receipts/debt/{id}', [ReceiptController::class, 'debt'])->name('receipts.debt');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
});