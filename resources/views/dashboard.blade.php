@extends('layouts.app')
@section('title', $trans['dashboard'])
@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>{{ $trans['dashboard'] }}</h1>
        <p>{{ $trans['welcome_back'] }}, {{ auth()->user()->name }} — {{ now()->format('l, d F Y') }}</p>
    </div>
</div>

<div class="grid-3" style="margin-bottom: 24px;">
    <div class="stat-card" style="border-left: 4px solid #1a3c5e; border-right: 4px solid #1a3c5e;">
        <div class="label">{{ $trans['total_clients'] }}</div>
        <div class="value" style="color: #1a3c5e;">{{ $totalClients }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #16a34a; border-right: 4px solid #16a34a;">
        <div class="label">{{ $trans['total_usd'] }}</div>
        <div class="value {{ $totalUsd < 0 ? 'amount-negative' : 'amount-positive' }}">$ {{ number_format($totalUsd, 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #2563eb; border-right: 4px solid #2563eb;">
        <div class="label">{{ $trans['total_iqd'] }}</div>
        <div class="value {{ $totalIqd < 0 ? 'amount-negative' : '' }}" style="color: #2563eb;">IQD {{ number_format($totalIqd, 0) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #d97706; border-right: 4px solid #d97706;">
        <div class="label">{{ $trans['total_eur'] }}</div>
        <div class="value {{ $totalEur < 0 ? 'amount-negative' : '' }}" style="color: #d97706;">€ {{ number_format($totalEur, 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #16a34a; border-right: 4px solid #16a34a;">
        <div class="label">{{ $trans['todays_deposits'] }}</div>
        <div class="value amount-positive">$ {{ number_format($todayDeposits, 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #dc2626; border-right: 4px solid #dc2626;">
        <div class="label">{{ $trans['todays_withdrawals'] }}</div>
        <div class="value amount-negative">$ {{ number_format($todayWithdrawals, 2) }}</div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding-bottom: 0;">
        <h2 style="font-size: 16px; font-weight: 600; color: #1a3c5e; margin-bottom: 16px;">{{ $trans['recent_transactions'] }}</h2>
    </div>
    @if($recentTransactions->count())
    <table class="table">
        <thead>
            <tr>
                <th>{{ $trans['date'] }}</th>
                <th>{{ $trans['clients'] }}</th>
                <th>{{ $trans['type'] }}</th>
                <th>{{ $trans['amount'] }} (USD)</th>
                <th>{{ $trans['after'] }} (USD)</th>
                <th>{{ $trans['cashier'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentTransactions as $tx)
            <tr>
                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td><a href="{{ route('clients.show', $tx->client_id) }}" style="color: #1a3c5e; font-weight: 500;">{{ $tx->client->full_name }}</a></td>
                <td>
                    <span class="badge {{ $tx->type === 'deposit' ? 'badge-success' : 'badge-danger' }}">
                        {{ $trans[$tx->type] }}
                    </span>
                </td>
                <td class="{{ $tx->type === 'withdrawal' ? 'amount-negative' : 'amount-positive' }}">$ {{ number_format($tx->amount, 2) }}</td>
                <td class="{{ $tx->balance_after < 0 ? 'amount-negative' : 'amount-positive' }}">$ {{ number_format($tx->balance_after, 2) }}</td>
                <td style="color: #666;">{{ $tx->createdBy->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 40px; color: #999; font-size: 14px;">
        {{ $trans['no_transactions'] }}
    </div>
    @endif
</div>
@endsection