<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ClientPortalController extends Controller
{
    public function showLogin()
    {
        if (Session::has('client_id')) {
            return redirect()->route('portal.dashboard');
        }
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        $client = Client::where('email', $request->login)
            ->orWhere('phone', $request->login)
            ->orWhere('phone', '+' . ltrim($request->login, '+'))
            ->orWhere('phone', ltrim($request->login, '+'))
            ->first();

        if (!$client || !$client->login_enabled) {
            return back()->withErrors(['login' => 'Account not found or login not enabled.']);
        }

        if (!Hash::check($request->password, $client->password)) {
            return back()->withErrors(['login' => 'Invalid password.']);
        }

        Session::put('client_id', $client->id);
        Session::put('client_name', $client->full_name);

        return redirect()->route('portal.dashboard');
    }

    public function dashboard(Request $request)
    {
        $clientId = Session::get('client_id');
        if (!$clientId) return redirect()->route('portal.login');

        $client   = Client::with(['accounts'])->findOrFail($clientId);
        $branchId = $client->branch_id;

        $usdCurrency = Currency::where('code', 'USD')->first();
        $rates       = $this->getRates($branchId, $usdCurrency);

        $usdAccount = $client->accounts->where('currency_id', $usdCurrency->id)->first();
        $usdBalance = $usdAccount ? $usdAccount->balance : 0;

        $balances = [
            'USD' => ['symbol' => '$',   'balance' => $usdBalance,                        'color' => '#16a34a'],
            'IQD' => ['symbol' => 'IQD', 'balance' => $usdBalance * ($rates['IQD'] ?? 0), 'color' => '#2563eb'],
            'EUR' => ['symbol' => '€',   'balance' => $usdBalance * ($rates['EUR'] ?? 0), 'color' => '#d97706'],
            'TRY' => ['symbol' => '₺',   'balance' => $usdBalance * ($rates['TRY'] ?? 0), 'color' => '#7c3aed'],
        ];

        // Date range filter
        $dateFrom = $request->date_from ?? null;
        $dateTo   = $request->date_to ?? null;

        $txQuery = Transaction::with(['currency', 'createdBy'])
            ->where('client_id', $clientId)
            ->latest();

        if ($dateFrom) $txQuery->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo)   $txQuery->whereDate('created_at', '<=', $dateTo);

        $transactions     = $txQuery->get();
        $totalDeposits    = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
        $net              = $totalDeposits - $totalWithdrawals;

        return view('portal.dashboard', compact(
            'client', 'balances', 'rates', 'transactions',
            'totalDeposits', 'totalWithdrawals', 'net',
            'dateFrom', 'dateTo'
        ));
    }

    public function logout()
    {
        Session::forget(['client_id', 'client_name']);
        return redirect()->route('portal.login');
    }

    private function getRates($branchId, $usdCurrency)
    {
        $rates = [];
        foreach (['IQD', 'EUR', 'TRY'] as $code) {
            $currency = Currency::where('code', $code)->first();
            $rate     = ExchangeRate::where('from_currency_id', $usdCurrency->id)
                ->where('to_currency_id', $currency->id)
                ->where('branch_id', $branchId)
                ->latest()->first();
            $rates[$code] = $rate ? $rate->rate : 0;
        }
        return $rates;
    }
}