<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Currency;
use App\Models\ClientAccount;
use App\Models\BailmentAccount;
use App\Models\BailmentTransaction;
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

        $client     = Client::with(['accounts'])->findOrFail($clientId);
        $currencies = Currency::whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();

        // Regular balances
        $balances = [];
        foreach ($currencies as $currency) {
            $account = ClientAccount::where('client_id', $clientId)
                ->where('currency_id', $currency->id)->first();
            $balances[$currency->code] = [
                'symbol'  => $currency->symbol,
                'balance' => $account ? $account->balance : 0,
                'color'   => $this->currencyColor($currency->code),
            ];
        }

        // Bailment balances
        $bailmentBalances = [];
        foreach ($currencies as $currency) {
            $account = BailmentAccount::where('client_id', $clientId)
                ->where('currency_id', $currency->id)->first();
            $bailmentBalances[$currency->code] = [
                'symbol'  => $currency->symbol,
                'balance' => $account ? $account->balance : 0,
                'color'   => $this->currencyColor($currency->code),
            ];
        }

        // Unseen notifications
        $unseenTransactions = Transaction::with(['currency'])
            ->where('client_id', $clientId)
            ->where('seen_by_client', false)
            ->latest()
            ->get();

        $unseenBailments = BailmentTransaction::with(['currency'])
            ->where('client_id', $clientId)
            ->where('seen_by_client', false)
            ->latest()
            ->get();

        // Date filter
        $dateFrom = $request->date_from ?? null;
        $dateTo   = $request->date_to ?? null;

        $txQuery = Transaction::with(['currency', 'createdBy'])
            ->where('client_id', $clientId)->latest();
        if ($dateFrom) $txQuery->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo)   $txQuery->whereDate('created_at', '<=', $dateTo);
        $transactions     = $txQuery->get();
        $totalDeposits    = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
        $net              = $totalDeposits - $totalWithdrawals;

        $bTxQuery = BailmentTransaction::with(['currency', 'createdBy'])
            ->where('client_id', $clientId)->latest();
        if ($dateFrom) $bTxQuery->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo)   $bTxQuery->whereDate('created_at', '<=', $dateTo);
        $bailmentTransactions     = $bTxQuery->get();
        $bailmentTotalDeposits    = $bailmentTransactions->where('type', 'deposit')->sum('amount');
        $bailmentTotalWithdrawals = $bailmentTransactions->where('type', 'withdrawal')->sum('amount');

        return view('portal.dashboard', compact(
            'client', 'balances', 'bailmentBalances',
            'transactions', 'bailmentTransactions',
            'totalDeposits', 'totalWithdrawals', 'net',
            'bailmentTotalDeposits', 'bailmentTotalWithdrawals',
            'dateFrom', 'dateTo', 'currencies',
            'unseenTransactions', 'unseenBailments'
        ));
    }

    public function markSeen(Request $request)
    {
        $clientId = Session::get('client_id');
        if (!$clientId) return response()->json(['error' => 'Unauthorized'], 401);

        Transaction::where('client_id', $clientId)
            ->where('seen_by_client', false)
            ->update(['seen_by_client' => true, 'seen_at' => now()]);

        BailmentTransaction::where('client_id', $clientId)
            ->where('seen_by_client', false)
            ->update(['seen_by_client' => true, 'seen_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function logout()
    {
        Session::forget(['client_id', 'client_name']);
        return redirect()->route('portal.login');
    }

    private function currencyColor($code)
    {
        return match($code) {
            'USD' => '#16a34a',
            'IQD' => '#2563eb',
            'EUR' => '#d97706',
            'TRY' => '#7c3aed',
            default => '#666'
        };
    }
}