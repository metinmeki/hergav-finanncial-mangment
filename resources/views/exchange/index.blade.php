@extends('layouts.app')
@section('title', $trans['exchange_rates'])
@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>{{ $trans['exchange_rates'] }}</h1>
        <p>{{ $trans['set_daily_rates'] }}</p>
    </div>
    <button class="btn-primary" onclick="document.getElementById('rateModal').style.display='flex'">+ {{ $trans['set_new_rate'] }}</button>
</div>

<div class="grid-3" style="margin-bottom: 24px;">
    @foreach($rates->take(3) as $rate)
    <div class="stat-card" style="border-left: 4px solid #1a3c5e; border-right: 4px solid #1a3c5e;">
        <div class="label">1 {{ $rate->fromCurrency->code }} = ? {{ $rate->toCurrency->code }}</div>
        <div class="value" style="color: #1a3c5e;">{{ number_format($rate->rate, 2) }}</div>
        <div style="font-size: 12px; color: #999; margin-top: 8px;">{{ $rate->rate_date->format('Y-m-d') }} — {{ $rate->setBy->name }}</div>
    </div>
    @endforeach
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>{{ $trans['from'] }}</th>
                <th>{{ $trans['to'] }}</th>
                <th>{{ $trans['rate'] }}</th>
                <th>{{ $trans['date'] }}</th>
                <th>{{ $trans['set_by'] }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rates as $rate)
            <tr>
                <td style="font-weight: 600; color: #1a3c5e;">{{ $rate->fromCurrency->code }}</td>
                <td style="font-weight: 600; color: #1a3c5e;">{{ $rate->toCurrency->code }}</td>
                <td style="font-weight: 700;">{{ number_format($rate->rate, 4) }}</td>
                <td style="color: #666;">{{ $rate->rate_date->format('Y-m-d') }}</td>
                <td style="color: #666;">{{ $rate->setBy->name }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align: center; color: #999; padding: 40px;">{{ $trans['no_transactions'] }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="rateModal" class="modal-overlay" style="display: none;">
    <div class="modal">
        <h2>{{ $trans['set_new_rate'] }}</h2>
        <form method="POST" action="{{ route('exchange.store') }}">
            @csrf
            <div class="grid-2">
                <div class="form-group">
                    <label>{{ $trans['from'] }}</label>
                    <select name="from_currency_id" class="form-control">
                        @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ $trans['to'] }}</label>
                    <select name="to_currency_id" class="form-control">
                        @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>{{ $trans['rate'] }}</label>
                <input type="number" name="rate" class="form-control" step="0.000001" placeholder="e.g. 1480" required>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="button" onclick="document.getElementById('rateModal').style.display='none'" class="btn-secondary" style="flex: 1; text-align: center;">{{ $trans['cancel'] }}</button>
                <button type="submit" class="btn-primary" style="flex: 1; text-align: center;">{{ $trans['save_rate'] }}</button>
            </div>
        </form>
    </div>
</div>
@endsection