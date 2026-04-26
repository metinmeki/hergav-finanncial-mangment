<?php

namespace App\Http\Controllers;
use App\Models\ReceiptCounter;

use App\Models\Transaction;
use App\Models\Client;
use App\Models\Currency;
use App\Models\ClientAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['client', 'currency', 'createdBy', 'transferToClient', 'transferFromClient'])
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

        $transactions     = $query->latest()->get();
        $totalDeposits    = $transactions->where('type', 'deposit')->sum('amount');
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
            'sender_name'   => 'required|string|min:2',
            'sender_phone'  => 'nullable|string',
        ]);

        DB::transaction(function() use ($request) {
            $branchId = Auth::user()->branch_id;
            $currency = Currency::where('code', $request->currency_code)->first();
            $client   = Client::findOrFail($request->client_id);

            $account = ClientAccount::firstOrCreate(
                ['client_id' => $client->id, 'currency_id' => $currency->id],
                ['balance' => 0]
            );

            $balanceBefore = $account->balance;

            if ($request->type === 'deposit') {
                $account->balance += $request->amount;
            } else {
                $account->balance -= $request->amount;
            }

            $account->save();

            Transaction::create([
                'receipt_number'    => ReceiptCounter::nextNumber(),
                'client_id'         => $client->id,
                'branch_id'         => $branchId,
                'currency_id'       => $currency->id,
                'type'              => $request->type,
                'amount'            => $request->amount,
                'balance_before'    => $balanceBefore,
                'balance_after'     => $account->balance,
                'original_currency' => $request->currency_code,
                'original_amount'   => $request->amount,
                'original_rate'     => 1,
                'sender_name'       => $request->sender_name,
                'sender_phone'      => $request->sender_phone,
                'notes'             => $request->notes,
                'reference_no'      => 'TXN-' . strtoupper(uniqid()),
                'created_by'        => Auth::id(),
                'seen_by_client'    => false,
            ]);
        });

        return redirect()->back()->with('success', 'Transaction recorded successfully.');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_client_id' => 'required|exists:clients,id',
            'to_client_id'   => 'required|exists:clients,id|different:from_client_id',
            'currency_code'  => 'required|in:USD,IQD,EUR,TRY',
            'amount'         => 'required|numeric|min:0.01',
            'notes'          => 'nullable|string',
        ]);

        DB::transaction(function() use ($request) {
            $branchId   = Auth::user()->branch_id;
            $currency   = Currency::where('code', $request->currency_code)->first();
            $fromClient = Client::findOrFail($request->from_client_id);
            $toClient   = Client::findOrFail($request->to_client_id);
            $reference  = 'TRF-' . strtoupper(uniqid());

            // From client account
            $fromAccount = ClientAccount::firstOrCreate(
                ['client_id' => $fromClient->id, 'currency_id' => $currency->id],
                ['balance' => 0]
            );
            $fromBalanceBefore    = $fromAccount->balance;
            $fromAccount->balance -= $request->amount;
            $fromAccount->save();

            // To client account
            $toAccount = ClientAccount::firstOrCreate(
                ['client_id' => $toClient->id, 'currency_id' => $currency->id],
                ['balance' => 0]
            );
            $toBalanceBefore    = $toAccount->balance;
            $toAccount->balance += $request->amount;
            $toAccount->save();

            // Transfer out for from client
            Transaction::create([
                'client_id'             => $fromClient->id,
                'branch_id'             => $branchId,
                'currency_id'           => $currency->id,
                'type'                  => 'transfer_out',
                'amount'                => $request->amount,
                'balance_before'        => $fromBalanceBefore,
                'balance_after'         => $fromAccount->balance,
                'transfer_to_client_id' => $toClient->id,
                'transfer_reference'    => $reference,
                'sender_name'           => $fromClient->full_name,
                'notes'                 => $request->notes,
                'reference_no'          => $reference,
                'created_by'            => Auth::id(),
                'seen_by_client'        => false,
            ]);

            // Transfer in for to client
            Transaction::create([
                'receipt_number' => ReceiptCounter::nextNumber(),
                'client_id'               => $toClient->id,
                'branch_id'               => $branchId,
                'currency_id'             => $currency->id,
                'type'                    => 'transfer_in',
                'amount'                  => $request->amount,
                'balance_before'          => $toBalanceBefore,
                'balance_after'           => $toAccount->balance,
                'transfer_from_client_id' => $fromClient->id,
                'transfer_reference'      => $reference,
                'sender_name'             => $fromClient->full_name,
                'notes'                   => $request->notes,
                'reference_no'            => $reference,
                'created_by'              => Auth::id(),
                'seen_by_client'          => false,
            ]);
        });

        return redirect()->back()->with('success', 'Transfer completed successfully.');
    }

    public function dailyReport(Request $request)
    {
        $dateFrom = $request->date_from ?? today()->format('Y-m-d');
        $dateTo   = $request->date_to ?? today()->format('Y-m-d');
        $branchId = Auth::user()->branch_id;

        $transactions = Transaction::with(['client', 'currency', 'createdBy', 'transferToClient', 'transferFromClient'])
            ->where('branch_id', $branchId)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('client_id')
            ->orderBy('created_at')
            ->get();

        $grouped          = $transactions->groupBy('client_id');
        $totalDeposits    = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
        $net              = $totalDeposits - $totalWithdrawals;

        return view('transactions.daily', compact(
            'transactions', 'grouped', 'dateFrom', 'dateTo',
            'totalDeposits', 'totalWithdrawals', 'net'
        ));
    }
}