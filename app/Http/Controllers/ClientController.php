<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Currency;
use App\Models\ClientAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['accounts.currency'])
            ->where('branch_id', Auth::user()->branch_id);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('code', 'ilike', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $clients    = $query->latest()->get();
        $currencies = Currency::whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();

        return view('clients.index', compact('clients', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'      => 'required|unique:clients,code',
            'full_name' => 'required',
        ]);

        $client = Client::create([
            'code'         => $request->code,
            'full_name'    => $request->full_name,
            'full_name_ar' => $request->full_name_ar,
            'phone'        => $request->phone,
            'address'      => $request->address,
            'national_id'  => $request->national_id,
            'branch_id'    => Auth::user()->branch_id,
            'status'       => 'active',
            'created_by'   => Auth::id(),
        ]);

        $currencies = Currency::whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();
        foreach ($currencies as $currency) {
            ClientAccount::create([
                'client_id'   => $client->id,
                'currency_id' => $currency->id,
                'balance'     => 0,
            ]);
        }

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client created successfully.');
    }

    public function show($id)
    {
        $client = Client::with([
            'accounts.currency',
            'transactions.currency',
            'transactions.createdBy',
            'transactions.transferToClient',
            'transactions.transferFromClient',
        ])
        ->where('branch_id', Auth::user()->branch_id)
        ->findOrFail($id);

        $currencies = Currency::whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();

        $balances = [];
        foreach ($currencies as $currency) {
            $account = $client->accounts->where('currency_id', $currency->id)->first();
            $balances[$currency->code] = [
                'symbol'  => $currency->symbol,
                'balance' => $account ? $account->balance : 0,
                'color'   => $this->currencyColor($currency->code),
            ];
        }

        return view('clients.show', compact('client', 'balances', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $client = Client::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $client->update([
            'full_name'    => $request->full_name,
            'full_name_ar' => $request->full_name_ar,
            'phone'        => $request->phone,
            'address'      => $request->address,
            'national_id'  => $request->national_id,
            'status'       => $request->status,
        ]);
        return redirect()->route('clients.show', $client->id)->with('success', 'Client updated.');
    }

    public function enableLogin(Request $request, $id)
    {
        $request->validate(['password' => 'required|min:6']);
        $client = Client::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $client->update([
            'email'         => $request->email ?? $client->email,
            'phone'         => $request->phone ?? $client->phone,
            'password'      => Hash::make($request->password),
            'login_enabled' => true,
        ]);
        return redirect()->route('clients.show', $client->id)->with('success', 'Client portal access enabled.');
    }

    public function disableLogin($id)
    {
        $client = Client::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $client->update(['login_enabled' => false, 'password' => null]);
        return redirect()->route('clients.show', $client->id)->with('success', 'Client portal access disabled.');
    }

    public function debt(Request $request)
    {
        $branchId   = Auth::user()->branch_id;
        $currencies = Currency::whereIn('code', ['USD', 'IQD', 'EUR', 'TRY'])->get();

        $query = Client::with(['accounts.currency'])
            ->where('branch_id', $branchId)
            ->whereHas('accounts', function($q) {
                $q->where('balance', '<', 0);
            });

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('code', 'ilike', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->client_id) {
            $query->where('id', $request->client_id);
        }

        $debtClients = $query->get();
        $allClients  = Client::where('branch_id', $branchId)->orderBy('full_name')->get();

        $totalDebts = [];
        foreach ($currencies as $currency) {
            $total = ClientAccount::where('currency_id', $currency->id)
                ->where('balance', '<', 0)
                ->whereHas('client', fn($q) => $q->where('branch_id', $branchId))
                ->sum('balance');
            $totalDebts[$currency->code] = [
                'symbol' => $currency->symbol,
                'total'  => $total,
            ];
        }

        return view('clients.debt', compact('debtClients', 'currencies', 'totalDebts', 'allClients'));
    }

    private function currencyColor($code)
    {
        return match($code) {
            'USD' => '#16a34a',
            'IQD' => '#2563eb',
            'EUR' => '#d97706',
            'TRY' => '#7c3aed',
            default => '#666',
        };
    }
}