<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Client;
use App\Models\Currency;
use App\Models\ClientAccount;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['client', 'currency', 'createdBy'])
            ->where('branch_id', Auth::user()->branch_id);

        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('client', function($q2) use ($request) {
                    $q2->where('full_name', 'ilike', '%' . $request->search . '%')
                       ->orWhere('code', 'ilike', '%' . $request->search . '%');
                })->orWhere('reference_no', 'ilike', '%' . $request->search . '%');
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        $transactions = $query->latest()->get();

        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');

        $clients = Client::where('branch_id', Auth::user()->branch_id)
            ->orderBy('full_name')->get();

        return view('transactions.index', compact(
            'transactions', 'totalDeposits', 'totalWithdrawals', 'clients'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'type'          => 'required|in:deposit,withdrawal',
            'currency_code' => 'required|in:USD,IQD,EUR,TRY',
            'amount'        => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function() use ($request) {
            $branchId    = Auth::user()->branch_id;
            $usdCurrency = Currency::where('code', 'USD')->first();
            $client      = Client::findOrFail($request->client_id);

            $incomingCode = $request->currency_code;
            $usdAmount    = $this->convertToUsd($incomingCode, $request->amount, $branchId);

            $account = ClientAccount::firstOrCreate(
                ['client_id' => $client->id, 'currency_id' => $usdCurrency->id],
                ['balance' => 0]
            );

            $balanceBefore = $account->balance;

            if ($request->type === 'deposit') {
                $account->balance += $usdAmount;
            } else {
                $account->balance -= $usdAmount;
            }

            $account->save();

            $rateUsed = $this->getRate($incomingCode, $branchId);

            Transaction::create([
                'client_id'         => $client->id,
                'branch_id'         => $branchId,
                'currency_id'       => $usdCurrency->id,
                'type'              => $request->type,
                'amount'            => $usdAmount,
                'balance_before'    => $balanceBefore,
                'balance_after'     => $account->balance,
                'original_currency' => $incomingCode,
                'original_amount'   => $request->amount,
                'original_rate'     => $rateUsed,
                'sender_name'       => $request->sender_name,
                'sender_phone'      => $request->sender_phone,
                'notes'             => $request->notes,
                'reference_no'      => 'TXN-' . strtoupper(uniqid()),
                'created_by'        => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Transaction recorded successfully.');
    }

   public function dailyReport(Request $request)
{
    $dateFrom = $request->date_from ?? today()->format('Y-m-d');
    $dateTo   = $request->date_to ?? today()->format('Y-m-d');
    $branchId = Auth::user()->branch_id;

    $transactions = Transaction::with(['client', 'currency', 'createdBy'])
        ->where('branch_id', $branchId)
        ->whereDate('created_at', '>=', $dateFrom)
        ->whereDate('created_at', '<=', $dateTo)
        ->orderBy('client_id')
        ->orderBy('created_at')
        ->get();

    $grouped = $transactions->groupBy('client_id');

    $totalDeposits    = $transactions->where('type', 'deposit')->sum('amount');
    $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
    $net              = $totalDeposits - $totalWithdrawals;

    $usdCurrency = Currency::where('code', 'USD')->first();
    $rates = [];
    foreach (['IQD', 'EUR', 'TRY'] as $code) {
        $currency = Currency::where('code', $code)->first();
        $rate = ExchangeRate::where('from_currency_id', $usdCurrency->id)
            ->where('branch_id', $branchId)
            ->whereHas('toCurrency', fn($q) => $q->where('code', $code))
            ->latest()->first();
        $rates[$code] = $rate ? $rate->rate : 0;
    }

    return view('transactions.daily', compact(
        'transactions', 'grouped', 'dateFrom', 'dateTo',
        'totalDeposits', 'totalWithdrawals', 'net', 'rates'
    ));
}
    private function convertToUsd($currencyCode, $amount, $branchId)
    {
        if ($currencyCode === 'USD') return $amount;

        $usd  = Currency::where('code', 'USD')->first();
        $from = Currency::where('code', $currencyCode)->first();

        $rate = ExchangeRate::where('from_currency_id', $usd->id)
            ->where('to_currency_id', $from->id)
            ->where('branch_id', $branchId)
            ->latest()->first();

        if ($rate) return $amount / $rate->rate;

        $reverseRate = ExchangeRate::where('from_currency_id', $from->id)
            ->where('to_currency_id', $usd->id)
            ->where('branch_id', $branchId)
            ->latest()->first();

        if ($reverseRate) return $amount * $reverseRate->rate;

        return $amount;
    }

    private function getRate($currencyCode, $branchId)
    {
        if ($currencyCode === 'USD') return 1;

        $usd  = Currency::where('code', 'USD')->first();
        $from = Currency::where('code', $currencyCode)->first();

        $rate = ExchangeRate::where('from_currency_id', $usd->id)
            ->where('to_currency_id', $from->id)
            ->where('branch_id', $branchId)
            ->latest()->first();

        return $rate ? $rate->rate : 1;
    }
}