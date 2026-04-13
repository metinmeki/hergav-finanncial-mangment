<?php

use App\Http\Controllers\ClientPortalController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\UserController;

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

// Client Portal routes
Route::get('/portal/login', [ClientPortalController::class, 'showLogin'])->name('portal.login');
Route::post('/portal/login', [ClientPortalController::class, 'login'])->name('portal.login.post');
Route::get('/portal/dashboard', [ClientPortalController::class, 'dashboard'])->name('portal.dashboard');
Route::post('/portal/logout', [ClientPortalController::class, 'logout'])->name('portal.logout');

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

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/daily', [TransactionController::class, 'dailyReport'])->name('transactions.daily');

    // Exchange rates
    Route::get('/exchange', [ExchangeRateController::class, 'index'])->name('exchange.index');
    Route::post('/exchange', [ExchangeRateController::class, 'store'])->name('exchange.store');
    Route::get('/exchange/rate/{fromId}/{toId}', [ExchangeRateController::class, 'getLatestRate'])->name('exchange.rate');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{id}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
});