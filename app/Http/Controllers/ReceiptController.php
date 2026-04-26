<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BailmentTransaction;
use App\Models\Client;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function transaction($id)
    {
        $tx = Transaction::with([
            'client', 'currency', 'createdBy',
            'transferToClient', 'transferFromClient'
        ])->findOrFail($id);

        return view('receipts.transaction', compact('tx'));
    }

    public function transfer($id)
    {
        $tx = Transaction::with([
            'client', 'currency', 'createdBy',
            'transferToClient', 'transferFromClient'
        ])->where('type', 'transfer_out')->findOrFail($id);

        $receiverTx = Transaction::with(['client', 'currency'])
            ->where('transfer_reference', $tx->transfer_reference)
            ->where('type', 'transfer_in')
            ->first();

        return view('receipts.transfer', compact('tx', 'receiverTx'));
    }

    public function bailment($id)
    {
        $tx = BailmentTransaction::with([
            'client', 'currency', 'createdBy'
        ])->findOrFail($id);

        return view('receipts.bailment', compact('tx'));
    }

    public function debt($id)
    {
        $client     = Client::with(['accounts.currency'])
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);
        $currencies = Currency::whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();

        $debts = [];
        foreach ($currencies as $currency) {
            $account = $client->accounts->where('currency_id', $currency->id)->first();
            $balance = $account ? $account->balance : 0;
            if ($balance < 0) {
                $debts[$currency->code] = [
                    'symbol'  => $currency->symbol,
                    'balance' => $balance,
                ];
            }
        }

        return view('receipts.debt', compact('client', 'debts', 'currencies'));
    }
}