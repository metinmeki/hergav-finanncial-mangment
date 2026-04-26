@extends('layouts.app')
@section('title', $isRTL ? 'تقرير المعاملات' : 'Transaction Report')
@section('content')

<div class="page-header">
    <div class="page-title">
        <h1>{{ $isRTL ? 'تقرير المعاملات' : 'Transaction Report' }}</h1>
        <p>{{ $dateFrom }} → {{ $dateTo }}</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('transactions.index') }}" class="btn-secondary">← {{ $trans['back'] }}</a>
        <button onclick="window.print()" class="btn-primary">🖨️ {{ $trans['print_statement'] }}</button>
    </div>
</div>

<!-- Date range picker -->
<div class="card card-body" style="margin-bottom: 16px;">
    <form method="GET" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 160px;">
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'من تاريخ' : 'From date' }}</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
        </div>
        <div style="flex: 1; min-width: 160px;">
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'إلى تاريخ' : 'To date' }}</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn-primary">{{ $isRTL ? 'عرض' : 'View' }}</button>
            <a href="?date_from={{ today()->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? 'اليوم' : 'Today' }}</a>
            <a href="?date_from={{ today()->subDays(6)->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? '7 أيام' : '7 days' }}</a>
            <a href="?date_from={{ today()->subDays(29)->format('Y-m-d') }}&date_to={{ today()->format('Y-m-d') }}" class="btn-secondary">{{ $isRTL ? '30 يوم' : '30 days' }}</a>
        </div>
    </form>
</div>

<!-- Summary -->
<div class="grid-3" style="margin-bottom: 24px;">
    <div class="stat-card" style="border-left: 4px solid #16a34a; border-right: 4px solid #16a34a;">
        <div class="label">{{ $isRTL ? 'إجمالي الإيداعات' : 'Total Deposits' }}</div>
        <div class="value amount-positive">$ {{ number_format($totalDeposits, 2) }}</div>
        <div style="font-size: 12px; color: #2563eb; margin-top: 4px;">IQD {{ number_format($totalDeposits * ($rates['IQD'] ?? 0), 0) }}</div>
        <div style="font-size: 12px; color: #d97706; margin-top: 2px;">€ {{ number_format($totalDeposits * ($rates['EUR'] ?? 0), 2) }}</div>
        <div style="font-size: 12px; color: #7c3aed; margin-top: 2px;">₺ {{ number_format($totalDeposits * ($rates['TRY'] ?? 0), 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #dc2626; border-right: 4px solid #dc2626;">
        <div class="label">{{ $isRTL ? 'إجمالي السحوبات' : 'Total Withdrawals' }}</div>
        <div class="value amount-negative">$ {{ number_format($totalWithdrawals, 2) }}</div>
        <div style="font-size: 12px; color: #2563eb; margin-top: 4px;">IQD {{ number_format($totalWithdrawals * ($rates['IQD'] ?? 0), 0) }}</div>
        <div style="font-size: 12px; color: #d97706; margin-top: 2px;">€ {{ number_format($totalWithdrawals * ($rates['EUR'] ?? 0), 2) }}</div>
        <div style="font-size: 12px; color: #7c3aed; margin-top: 2px;">₺ {{ number_format($totalWithdrawals * ($rates['TRY'] ?? 0), 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #1a3c5e; border-right: 4px solid #1a3c5e;">
        <div class="label">{{ $isRTL ? 'الصافي' : 'Net Movement' }}</div>
        <div class="value {{ $net < 0 ? 'amount-negative' : 'amount-positive' }}">$ {{ number_format($net, 2) }}</div>
        <div style="font-size: 12px; color: #2563eb; margin-top: 4px;">IQD {{ number_format($net * ($rates['IQD'] ?? 0), 0) }}</div>
        <div style="font-size: 12px; color: #d97706; margin-top: 2px;">€ {{ number_format($net * ($rates['EUR'] ?? 0), 2) }}</div>
        <div style="font-size: 12px; color: #7c3aed; margin-top: 2px;">₺ {{ number_format($net * ($rates['TRY'] ?? 0), 2) }}</div>
    </div>
</div>

@if($grouped->count() > 0)
    @foreach($grouped as $clientId => $clientTxs)
    @php $client = $clientTxs->first()->client; @endphp
    <div class="card" style="margin-bottom: 16px;">
        <div style="padding: 16px 24px; background: #f8f9fa; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="{{ route('clients.show', $clientId) }}" style="font-size: 15px; font-weight: 600; color: #1a3c5e; text-decoration: none;">{{ $client->full_name }}</a>
                <span style="font-size: 13px; color: #999; margin-left: 8px;">{{ $client->code }}</span>
            </div>
            <div style="display: flex; gap: 16px; font-size: 13px;">
                <span class="amount-positive">↓ $ {{ number_format($clientTxs->where('type', 'deposit')->sum('amount'), 2) }}</span>
                <span class="amount-negative">↑ $ {{ number_format($clientTxs->where('type', 'withdrawal')->sum('amount'), 2) }}</span>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>{{ $isRTL ? 'التاريخ والوقت' : 'Date & Time' }}</th>
                    <th>{{ $trans['type'] }}</th>
                    <th>{{ $trans['original'] }}</th>
                    <th>USD</th>
                    <th>IQD</th>
                    <th>EUR</th>
                    <th>TRY</th>
                    <th>{{ $trans['before'] }}</th>
                    <th>{{ $trans['after'] }}</th>
                    <th>{{ $isRTL ? 'اسم المرسل / المستلم' : 'Sender / Receiver' }}</th>
                    <th>{{ $trans['notes'] }}</th>
                    <th>{{ $trans['cashier'] }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientTxs as $tx)
                <tr>
                    <td style="color: #666; font-size: 12px; white-space: nowrap;">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <span class="badge {{ $tx->type === 'deposit' ? 'badge-success' : 'badge-danger' }}">
                            {{ $trans[$tx->type] }}
                        </span>
                    </td>
                    <td style="font-weight: 600; color: #1a3c5e;">
                        {{ $tx->original_currency ?? 'USD' }} {{ number_format($tx->original_amount ?? $tx->amount, 2) }}
                    </td>
                    <td class="{{ $tx->type === 'withdrawal' ? 'amount-negative' : 'amount-positive' }}">
                        $ {{ number_format($tx->amount, 2) }}
                    </td>
                    <td style="color: #2563eb;">IQD {{ number_format($tx->amount * ($rates['IQD'] ?? 0), 0) }}</td>
                    <td style="color: #d97706;">€ {{ number_format($tx->amount * ($rates['EUR'] ?? 0), 2) }}</td>
                    <td style="color: #7c3aed;">₺ {{ number_format($tx->amount * ($rates['TRY'] ?? 0), 2) }}</td>
                    <td style="color: #666;">$ {{ number_format($tx->balance_before, 2) }}</td>
                    <td class="{{ $tx->balance_after < 0 ? 'amount-negative' : 'amount-positive' }}">
                        $ {{ number_format($tx->balance_after, 2) }}
                    </td>
                    <td>
                        <div style="font-size: 13px; font-weight: 600; color: #1a3c5e;">{{ $tx->sender_name ?? '—' }}</div>
                        @if($tx->sender_phone)
                        <div style="font-size: 12px; color: #999;">{{ $tx->sender_phone }}</div>
                        @endif
                    </td>
                    <td style="color: #666; font-size: 12px;">{{ $tx->notes ?? '—' }}</td>
                    <td style="color: #666; font-size: 12px;">{{ $tx->createdBy->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
@else
    <div class="card card-body" style="text-align: center; padding: 60px; color: #999;">
        {{ $isRTL ? 'لا توجد معاملات في هذه الفترة' : 'No transactions found for this period.' }}
    </div>
@endif

@endsection

@section('scripts')
<style>
    @media print {
        .sidebar, .page-header a, .page-header button, form, .btn-secondary { display: none !important; }
        .main { margin: 0 !important; padding: 20px !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; margin-bottom: 10px !important; }
    }
</style>
@endsection