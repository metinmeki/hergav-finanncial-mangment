<?php

namespace App\Http\Controllers;
use App\Models\ReceiptCounter;

use App\Models\BailmentAccount;
use App\Models\BailmentTransaction;
use App\Models\Client;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BailmentController extends Controller
{
    public function index(Request $request)
    {
        $branchId   = Auth::user()->branch_id;
        $currencies = Currency::whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();

        $query = BailmentTransaction::with(['client', 'currency', 'createdBy'])
            ->whereHas('client', fn($q) => $q->where('branch_id', $branchId));

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('client', function($q2) use ($request) {
                    $q2->where('full_name', 'ilike', '%' . $request->search . '%')
                       ->orWhere('code', 'ilike', '%' . $request->search . '%');
                })->orWhere('sender_name', 'ilike', '%' . $request->search . '%');
            });
        }

        if ($request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $transactions     = $query->latest()->get();
        $totalDeposits    = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');

        $clients = Client::where('branch_id', $branchId)->orderBy('full_name')->get();

        $totalBailments = [];
        foreach ($currencies as $currency) {
            $total = BailmentAccount::where('currency_id', $currency->id)
                ->whereHas('client', fn($q) => $q->where('branch_id', $branchId))
                ->sum('balance');
            $totalBailments[$currency->code] = [
                'symbol' => $currency->symbol,
                'total'  => $total,
            ];
        }

        return view('bailment.index', compact(
            'transactions', 'clients', 'currencies',
            'totalDeposits', 'totalWithdrawals', 'totalBailments'
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

            $account = BailmentAccount::firstOrCreate(
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

            BailmentTransaction::create([
                'receipt_number' => ReceiptCounter::nextNumber(),
                'client_id'      => $client->id,
                'branch_id'      => $branchId,
                'currency_id'    => $currency->id,
                'type'           => $request->type,
                'amount'         => $request->amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $account->balance,
                'sender_name'    => $request->sender_name,
                'sender_phone'   => $request->sender_phone,
                'notes'          => $request->notes,
                'reference_no'   => 'BAI-' . strtoupper(uniqid()),
                'created_by'     => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Bailment transaction recorded successfully.');
    }

    public function clientBailment($clientId)
    {
        $branchId   = Auth::user()->branch_id;
        $client     = Client::where('branch_id', $branchId)->findOrFail($clientId);
        $currencies = Currency::whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();

        $balances = [];
        foreach ($currencies as $currency) {
            $account = BailmentAccount::where('client_id', $client->id)
                ->where('currency_id', $currency->id)
                ->first();
            $balances[$currency->code] = [
                'symbol'  => $currency->symbol,
                'balance' => $account ? $account->balance : 0,
                'color'   => match($currency->code) {
                    'USD' => '#16a34a',
                    'IQD' => '#2563eb',
                    'EUR' => '#d97706',
                    'TRY' => '#7c3aed',
                    default => '#666'
                },
            ];
        }

        $transactions = BailmentTransaction::with(['currency', 'createdBy'])
            ->where('client_id', $clientId)
            ->latest()
            ->get();

        return view('bailment.client', compact('client', 'balances', 'currencies', 'transactions'));
    }
}