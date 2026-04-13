<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Currency;
use App\Models\ClientAccount;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    private function getRates($branchId)
    {
        $usd = Currency::where('code', 'USD')->first();
        $rates = [];
        foreach (['IQD', 'EUR', 'TRY'] as $code) {
            $currency = Currency::where('code', $code)->first();
            $rate = ExchangeRate::where('from_currency_id', $usd->id)
                ->where('to_currency_id', $currency->id)
                ->where('branch_id', $branchId)
                ->latest()->first();
            $rates[$code] = $rate ? $rate->rate : 0;
        }
        return $rates;
    }

    public function index(Request $request)
    {
        $query = Client::with(['accounts'])
            ->where('branch_id', Auth::user()->branch_id);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('code', 'ilike', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query->latest()->get();
        $rates   = $this->getRates(Auth::user()->branch_id);

        return view('clients.index', compact('clients', 'rates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'      => 'required|unique:clients,code',
            'full_name' => 'required',
        ]);

        $client = Client::create([
            'code'        => $request->code,
            'full_name'   => $request->full_name,
            'full_name_ar'=> $request->full_name_ar,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'national_id' => $request->national_id,
            'branch_id'   => Auth::user()->branch_id,
            'status'      => 'active',
            'created_by'  => Auth::id(),
        ]);

        $usdCurrency = Currency::where('code', 'USD')->first();
        ClientAccount::create([
            'client_id'   => $client->id,
            'currency_id' => $usdCurrency->id,
            'balance'     => 0,
        ]);

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client created successfully.');
    }

    public function show($id)
    {
        $client = Client::with(['accounts.currency', 'transactions', 'transactions.createdBy'])
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        $branchId    = Auth::user()->branch_id;
        $usdCurrency = Currency::where('code', 'USD')->first();
        $rates       = $this->getRates($branchId);

        $usdAccount  = $client->accounts->where('currency_id', $usdCurrency->id)->first();
        $usdBalance  = $usdAccount ? $usdAccount->balance : 0;

        $balances = [
            'USD' => ['symbol' => '$',   'balance' => $usdBalance,                        'color' => '#16a34a'],
            'IQD' => ['symbol' => 'IQD', 'balance' => $usdBalance * ($rates['IQD'] ?? 0), 'color' => '#2563eb'],
            'EUR' => ['symbol' => '€',   'balance' => $usdBalance * ($rates['EUR'] ?? 0), 'color' => '#d97706'],
            'TRY' => ['symbol' => '₺',   'balance' => $usdBalance * ($rates['TRY'] ?? 0), 'color' => '#7c3aed'],
        ];

        $currencies = Currency::all();

        return view('clients.show', compact('client', 'balances', 'rates', 'currencies', 'usdBalance'));
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
        $request->validate([
            'password' => 'required|min:6',
        ]);

        $client = Client::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $client->update([
            'email'         => $request->email ?? $client->email,
            'phone'         => $request->phone ?? $client->phone,
            'password'      => Hash::make($request->password),
            'login_enabled' => true,
        ]);

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client portal access enabled.');
    }

    public function disableLogin($id)
    {
        $client = Client::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $client->update([
            'login_enabled' => false,
            'password'      => null,
        ]);

        return redirect()->route('clients.show', $client->id)
            ->with('success', 'Client portal access disabled.');
    }
}