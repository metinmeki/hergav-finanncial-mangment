@extends('layouts.app')
@section('title', $trans['transactions'])
@section('content')

<div class="page-header">
    <div class="page-title">
        <h1>{{ $trans['transactions'] }}</h1>
        <p>{{ $transactions->count() }} {{ $trans['transactions'] }}</p>
    </div>
    <a href="{{ route('transactions.daily') }}" class="btn-secondary">📅 {{ $isRTL ? 'التقرير اليومي' : 'Daily Report' }}</a>
</div>

<!-- Filters -->
<div class="card card-body" style="margin-bottom: 16px;">
    <form method="GET" style="display: grid; grid-template-columns: repeat(4, 1fr) auto; gap: 12px; align-items: end;">
        <div>
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'بحث' : 'Search' }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $isRTL ? 'اسم العميل أو المرجع' : 'Client name or reference' }}" class="form-control">
        </div>
        <div>
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'العميل' : 'Client' }}</label>
            <select name="client_id" class="form-control">
                <option value="">{{ $isRTL ? 'كل العملاء' : 'All clients' }}</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'من تاريخ' : 'From date' }}</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
        </div>
        <div>
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'إلى تاريخ' : 'To date' }}</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn-primary" style="padding: 10px 16px;">{{ $isRTL ? 'بحث' : 'Filter' }}</button>
            <a href="{{ route('transactions.index') }}" class="btn-secondary" style="padding: 10px 16px;">{{ $isRTL ? 'إعادة' : 'Reset' }}</a>
        </div>
    </form>

    <!-- Type filter -->
    <div style="display: flex; gap: 8px; margin-top: 12px;">
        @foreach(['all', 'deposit', 'withdrawal'] as $type)
        <a href="?type={{ $type }}&{{ http_build_query(request()->except('type')) }}"
           style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;
                  background: {{ request('type', 'all') === $type ? '#1a3c5e' : 'white' }};
                  color: {{ request('type', 'all') === $type ? 'white' : '#444' }};
                  border: 1px solid #e0e0e0;">
            {{ $trans[$type] }}
        </a>
        @endforeach
    </div>
</div>

<!-- Summary -->
<div class="grid-3" style="margin-bottom: 16px;">
    <div class="stat-card" style="border-left: 4px solid #16a34a; border-right: 4px solid #16a34a;">
        <div class="label">{{ $isRTL ? 'إجمالي الإيداعات' : 'Total Deposits' }}</div>
        <div class="value amount-positive">$ {{ number_format($totalDeposits, 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #dc2626; border-right: 4px solid #dc2626;">
        <div class="label">{{ $isRTL ? 'إجمالي السحوبات' : 'Total Withdrawals' }}</div>
        <div class="value amount-negative">$ {{ number_format($totalWithdrawals, 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #1a3c5e; border-right: 4px solid #1a3c5e;">
        <div class="label">{{ $isRTL ? 'الصافي' : 'Net' }}</div>
        <div class="value {{ ($totalDeposits - $totalWithdrawals) < 0 ? 'amount-negative' : 'amount-positive' }}">
            $ {{ number_format($totalDeposits - $totalWithdrawals, 2) }}
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>{{ $trans['date'] }}</th>
                <th>{{ $trans['clients'] }}</th>
                <th>{{ $trans['type'] }}</th>
                <th>{{ $trans['original'] }}</th>
                <th>USD</th>
                <th>IQD</th>
                <th>EUR</th>
                <th>TRY</th>
                <th>{{ $trans['before'] }}</th>
                <th>{{ $trans['after'] }}</th>
                <th>{{ $trans['sender'] }}</th>
                <th>{{ $isRTL ? 'المرجع' : 'Reference' }}</th>
                <th>{{ $trans['cashier'] }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
            @php
                $branchRates = [];
                // rates are shared from middleware but we need them here
            @endphp
            <tr>
                <td style="color: #666; font-size: 12px; white-space: nowrap;">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td><a href="{{ route('clients.show', $tx->client_id) }}" style="color: #1a3c5e; font-weight: 500;">{{ $tx->client->full_name }}</a></td>
                <td><span class="badge {{ $tx->type === 'deposit' ? 'badge-success' : 'badge-danger' }}">{{ $trans[$tx->type] }}</span></td>
                <td style="font-weight: 600; color: #1a3c5e;">{{ $tx->original_currency }} {{ number_format($tx->original_amount, 2) }}</td>
                <td class="{{ $tx->type === 'withdrawal' ? 'amount-negative' : 'amount-positive' }}">$ {{ number_format($tx->amount, 2) }}</td>
                <td style="color: #2563eb; font-size: 12px;">{{ number_format($tx->amount * ($tx->exchange_rate ?? 0), 0) }}</td>
                <td style="color: #d97706; font-size: 12px;">€ {{ number_format($tx->amount * 0, 2) }}</td>
                <td style="color: #7c3aed; font-size: 12px;">₺ {{ number_format($tx->amount * 0, 2) }}</td>
                <td style="color: #666;">$ {{ number_format($tx->balance_before, 2) }}</td>
                <td class="{{ $tx->balance_after < 0 ? 'amount-negative' : 'amount-positive' }}">$ {{ number_format($tx->balance_after, 2) }}</td>
                <td style="color: #666; font-size: 12px;">{{ $tx->sender_name ?? '—' }}</td>
                <td style="color: #999; font-size: 11px;">{{ $tx->reference_no }}</td>
                <td style="color: #666;">{{ $tx->createdBy->name }}</td>
            </tr>
            @empty
            <tr><td colspan="13" style="text-align: center; color: #999; padding: 40px;">{{ $trans['no_transactions'] }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection