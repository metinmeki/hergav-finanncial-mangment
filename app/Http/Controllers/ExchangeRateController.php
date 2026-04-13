<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRate::with(['fromCurrency', 'toCurrency', 'setBy'])
            ->where('branch_id', Auth::user()->branch_id)
            ->latest()
            ->get();

        $currencies = Currency::all();

        return view('exchange.index', compact('rates', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
            'rate' => 'required|numeric|min:0.000001',
        ]);

        ExchangeRate::create([
            'from_currency_id' => $request->from_currency_id,
            'to_currency_id' => $request->to_currency_id,
            'rate' => $request->rate,
            'rate_date' => today(),
            'set_by' => Auth::id(),
            'branch_id' => Auth::user()->branch_id,
        ]);

        return redirect()->route('exchange.index')
            ->with('success', 'Exchange rate set successfully.');
    }

    public function getLatestRate($fromId, $toId)
    {
        $rate = ExchangeRate::where('from_currency_id', $fromId)
            ->where('to_currency_id', $toId)
            ->where('branch_id', Auth::user()->branch_id)
            ->latest()
            ->first();

        return response()->json(['rate' => $rate ? $rate->rate : null]);
    }
}