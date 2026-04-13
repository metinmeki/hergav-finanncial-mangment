<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Transaction;
use App\Models\ClientAccount;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $totalClients = Client::where('branch_id', $branchId)->count();

        $usdCurrency = Currency::where('code', 'USD')->first();

        $totalUsd = ClientAccount::where('currency_id', $usdCurrency->id)
            ->whereHas('client', fn($q) => $q->where('branch_id', $branchId))
            ->sum('balance');

        $usdToIqd = ExchangeRate::where('from_currency_id', $usdCurrency->id)
            ->where('branch_id', $branchId)
            ->whereHas('toCurrency', fn($q) => $q->where('code', 'IQD'))
            ->latest()->first();

        $usdToEur = ExchangeRate::where('from_currency_id', $usdCurrency->id)
            ->where('branch_id', $branchId)
            ->whereHas('toCurrency', fn($q) => $q->where('code', 'EUR'))
            ->latest()->first();

        $totalIqd = $usdToIqd ? $totalUsd * $usdToIqd->rate : 0;
        $totalEur = $usdToEur ? $totalUsd * $usdToEur->rate : 0;

        $todayDeposits = Transaction::where('branch_id', $branchId)
            ->where('type', 'deposit')
            ->whereDate('created_at', today())
            ->sum('amount');

        $todayWithdrawals = Transaction::where('branch_id', $branchId)
            ->where('type', 'withdrawal')
            ->whereDate('created_at', today())
            ->sum('amount');

        $recentTransactions = Transaction::with(['client', 'currency', 'createdBy'])
            ->where('branch_id', $branchId)
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalClients',
            'totalUsd',
            'totalIqd',
            'totalEur',
            'todayDeposits',
            'todayWithdrawals',
            'recentTransactions'
        ));
    }
}