@extends('layouts.app')
@section('title', $isRTL ? 'الديون' : 'Debts')
@section('content')

<div class="page-header">
    <div class="page-title">
        <h1>{{ $isRTL ? 'الديون' : 'Debts' }}</h1>
        <p>{{ $debtClients->count() }} {{ $isRTL ? 'عميل مدين' : 'clients in debt' }}</p>
    </div>
</div>

<!-- Total debt summary -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    @foreach($totalDebts as $code => $debt)
    <div class="stat-card" style="border-left: 4px solid #dc2626; border-right: 4px solid #dc2626;">
        <div class="label">{{ $isRTL ? 'إجمالي ديون' : 'Total' }} {{ $code }}</div>
        <div class="value amount-negative">{{ $debt['symbol'] }} {{ number_format(abs($debt['total']), $code === 'IQD' ? 0 : 2) }}</div>
    </div>
    @endforeach
</div>

<!-- Search and filter -->
<div class="card card-body" style="margin-bottom: 16px;">
    <form method="GET" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'بحث' : 'Search' }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $isRTL ? 'البحث بالاسم أو الرمز' : 'Search by name or code' }}" class="form-control">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'اختر عميل' : 'Select Client' }}</label>
            <select name="client_id" class="form-control">
                <option value="">{{ $isRTL ? 'كل العملاء المدينين' : 'All debt clients' }}</option>
                @foreach($allClients as $c)
                <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->code }})</option>
                @endforeach
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn-primary">{{ $isRTL ? 'بحث' : 'Search' }}</button>
            <a href="{{ route('clients.debt') }}" class="btn-secondary">{{ $isRTL ? 'إعادة' : 'Reset' }}</a>
        </div>
    </form>
</div>

<!-- Debt table -->
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>{{ $isRTL ? 'الرمز' : 'Code' }}</th>
                <th>{{ $isRTL ? 'الاسم' : 'Name' }}</th>
                <th>{{ $isRTL ? 'الهاتف' : 'Phone' }}</th>
                @foreach($currencies as $currency)
                <th style="color: {{ match($currency->code) { 'USD' => '#16a34a', 'IQD' => '#2563eb', 'EUR' => '#d97706', 'TRY' => '#7c3aed', default => '#666' } }};">
                    {{ $currency->code }} {{ $isRTL ? 'الدين' : 'Debt' }}
                </th>
                @endforeach
                <th>{{ $isRTL ? 'إجراء' : 'Action' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($debtClients as $client)
            <tr>
                <td style="font-weight: 600; color: #1a3c5e;">{{ $client->code }}</td>
                <td style="font-weight: 500;">{{ $client->full_name }}</td>
                <td style="color: #666;">{{ $client->phone }}</td>
                @foreach($currencies as $currency)
                @php
                    $account = $client->accounts->where('currency_id', $currency->id)->first();
                    $balance = $account ? $account->balance : 0;
                @endphp
                <td>
                    @if($balance < 0)
                        <span class="amount-negative" style="font-weight: 700;">
                            {{ $currency->symbol }} {{ number_format(abs($balance), $currency->code === 'IQD' ? 0 : 2) }}
                        </span>
                        <span style="font-size: 11px; background: #fef2f2; color: #dc2626; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">
                            {{ $isRTL ? 'مدين' : 'DEBT' }}
                        </span>
                    @else
                        <span style="color: #999; font-size: 12px;">—</span>
                    @endif
                </td>
                @endforeach
                <td style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('clients.show', $client->id) }}" class="btn-primary" style="padding: 6px 14px; font-size: 12px;">
                        {{ $isRTL ? 'عرض' : 'View' }}
                    </a>
                    <a href="{{ route('receipts.debt', $client->id) }}" target="_blank" class="btn-secondary" style="padding: 6px 14px; font-size: 12px; color: #dc2626; border-color: #dc2626;">
                        🖨️ {{ $isRTL ? 'وصل الدين' : 'Debt Receipt' }}
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 4 + $currencies->count() }}" style="text-align: center; color: #999; padding: 40px;">
                    {{ $isRTL ? 'لا يوجد عملاء مدينون' : 'No clients in debt' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection