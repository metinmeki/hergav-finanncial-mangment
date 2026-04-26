@extends('layouts.app')
@section('title', $isRTL ? 'الأمانات' : 'Bailment')
@section('content')

<div class="page-header">
    <div class="page-title">
        <h1>{{ $isRTL ? 'الأمانات' : 'Bailment' }}</h1>
        <p>{{ $isRTL ? 'إدارة أموال الأمانات' : 'Manage bailment funds' }}</p>
    </div>
    <button class="btn-primary" onclick="document.getElementById('addModal').style.display='flex'">
        + {{ $isRTL ? 'إضافة أمانة' : 'Add Bailment' }}
    </button>
</div>

<!-- Total bailment per currency -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    @foreach($totalBailments as $code => $b)
    <div class="stat-card" style="border-left: 4px solid {{ match($code) { 'USD' => '#16a34a', 'IQD' => '#2563eb', 'EUR' => '#d97706', 'TRY' => '#7c3aed', default => '#666' } }}; border-right: 4px solid {{ match($code) { 'USD' => '#16a34a', 'IQD' => '#2563eb', 'EUR' => '#d97706', 'TRY' => '#7c3aed', default => '#666' } }};">
        <div class="label">{{ $isRTL ? 'إجمالي أمانات' : 'Total' }} {{ $code }}</div>
        <div class="value" style="color: {{ match($code) { 'USD' => '#16a34a', 'IQD' => '#2563eb', 'EUR' => '#d97706', 'TRY' => '#7c3aed', default => '#666' } }};">
            {{ $b['symbol'] }} {{ number_format($b['total'], $code === 'IQD' ? 0 : 2) }}
        </div>
    </div>
    @endforeach
</div>

<!-- Filters -->
<div class="card card-body" style="margin-bottom: 16px;">
    <form method="GET" style="display: grid; grid-template-columns: repeat(4, 1fr) auto; gap: 12px; align-items: end;">
        <div>
            <label style="font-size: 12px; color: #666; display: block; margin-bottom: 4px;">{{ $isRTL ? 'بحث' : 'Search' }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $isRTL ? 'اسم العميل أو المرسل' : 'Client or sender name' }}" class="form-control">
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
            <a href="{{ route('bailment.index') }}" class="btn-secondary" style="padding: 10px 16px;">{{ $isRTL ? 'إعادة' : 'Reset' }}</a>
        </div>
    </form>

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
        <div class="value amount-positive">{{ number_format($totalDeposits, 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #dc2626; border-right: 4px solid #dc2626;">
        <div class="label">{{ $isRTL ? 'إجمالي السحوبات' : 'Total Withdrawals' }}</div>
        <div class="value amount-negative">{{ number_format($totalWithdrawals, 2) }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #1a3c5e; border-right: 4px solid #1a3c5e;">
        <div class="label">{{ $isRTL ? 'عدد المعاملات' : 'Total Transactions' }}</div>
        <div class="value" style="color: #1a3c5e;">{{ $transactions->count() }}</div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>{{ $trans['date'] }}</th>
                <th>{{ $isRTL ? 'العميل' : 'Client' }}</th>
                <th>{{ $trans['type'] }}</th>
                <th>{{ $trans['currency'] }}</th>
                <th>{{ $trans['amount'] }}</th>
                <th>{{ $trans['before'] }}</th>
                <th>{{ $trans['after'] }}</th>
                <th>{{ $isRTL ? 'اسم المرسل / المستلم' : 'Sender / Receiver' }}</th>
                <th>{{ $trans['notes'] }}</th>
                <th>{{ $trans['cashier'] }}</th>
                <th>{{ $isRTL ? 'وصل' : 'Receipt' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
            <tr>
                <td style="color: #666; font-size: 12px; white-space: nowrap;">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ route('bailment.client', $tx->client_id) }}" style="color: #1a3c5e; font-weight: 500;">{{ $tx->client->full_name }}</a>
                    <div style="font-size: 11px; color: #999;">{{ $tx->client->code }}</div>
                </td>
                <td><span class="badge {{ $tx->type === 'deposit' ? 'badge-success' : 'badge-danger' }}">{{ $trans[$tx->type] }}</span></td>
                <td style="font-weight: 600; color: #1a3c5e;">{{ $tx->currency->code }}</td>
                <td class="{{ $tx->type === 'withdrawal' ? 'amount-negative' : 'amount-positive' }}">
                    {{ $tx->currency->symbol }} {{ number_format($tx->amount, $tx->currency->code === 'IQD' ? 0 : 2) }}
                </td>
                <td style="color: #666;">{{ number_format($tx->balance_before, 2) }}</td>
                <td class="{{ $tx->balance_after < 0 ? 'amount-negative' : 'amount-positive' }}">
                    {{ number_format($tx->balance_after, 2) }}
                </td>
                <td>
                    <div style="font-size: 13px; font-weight: 600; color: #1a3c5e;">{{ $tx->sender_name }}</div>
                    @if($tx->sender_phone)
                    <div style="font-size: 12px; color: #999;">{{ $tx->sender_phone }}</div>
                    @endif
                </td>
                <td style="color: #666; font-size: 12px;">{{ $tx->notes ?? '—' }}</td>
                <td style="color: #666; font-size: 12px;">{{ $tx->createdBy->name }}</td>
                <td>
                    <a href="{{ route('receipts.bailment', $tx->id) }}" target="_blank"
                       style="padding: 4px 10px; background: #d97706; color: white; border-radius: 6px; font-size: 11px; text-decoration: none; white-space: nowrap;">
                        🖨️ {{ $isRTL ? 'وصل' : 'Receipt' }}
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align: center; color: #999; padding: 40px;">
                    {{ $isRTL ? 'لا توجد معاملات أمانات' : 'No bailment transactions yet.' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Add Bailment Modal -->
<div id="addModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="width: 500px;">
        <h2>{{ $isRTL ? 'إضافة أمانة' : 'Add Bailment Transaction' }}</h2>
        <form method="POST" action="{{ route('bailment.store') }}">
            @csrf

            <div class="form-group">
                <label>{{ $isRTL ? 'العميل' : 'Client' }} <span style="color: #dc2626;">*</span></label>
                <select name="client_id" class="form-control" required>
                    <option value="">{{ $isRTL ? 'اختر العميل' : 'Select client' }}</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->full_name }} ({{ $client->code }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ $trans['type'] }}</label>
                <div style="display: flex; gap: 8px;">
                    <label style="flex:1; text-align:center; padding:10px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="bbtn-deposit">
                        <input type="radio" name="type" value="deposit" style="display:none;" onchange="selectBType('deposit')" checked>
                        ↓ {{ $trans['deposit'] }}
                    </label>
                    <label style="flex:1; text-align:center; padding:10px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="bbtn-withdrawal">
                        <input type="radio" name="type" value="withdrawal" style="display:none;" onchange="selectBType('withdrawal')">
                        ↑ {{ $trans['withdrawal'] }}
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['currency'] }}</label>
                <div style="display: flex; gap: 8px;">
                    @foreach($currencies as $currency)
                    <label style="flex:1; text-align:center; padding:8px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="bcur-{{ $currency->code }}">
                        <input type="radio" name="currency_code" value="{{ $currency->code }}" style="display:none;" onchange="selectBCurrency('{{ $currency->code }}')" {{ $currency->code === 'USD' ? 'checked' : '' }}>
                        {{ $currency->symbol }} {{ $currency->code }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['amount'] }} <span style="color: #dc2626;">*</span></label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>{{ $isRTL ? 'اسم المرسل / المستلم' : 'Sender / Receiver Name' }} <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="sender_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>{{ $isRTL ? 'هاتف المرسل' : 'Sender Phone' }}</label>
                    <input type="text" name="sender_phone" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['notes_optional'] }}</label>
                <input type="text" name="notes" class="form-control">
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn-secondary" style="flex:1; text-align:center;">{{ $trans['cancel'] }}</button>
                <button type="submit" id="bsubmitBtn" class="btn-primary" style="flex:1; text-align:center; background:#16a34a;">{{ $trans['confirm_save'] }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function selectBType(type) {
    ['deposit','withdrawal'].forEach(t => {
        const btn = document.getElementById('bbtn-' + t);
        btn.style.borderColor = '#e0e0e0';
        btn.style.color = '#444';
        btn.style.background = 'white';
    });
    const colors = { deposit: '#16a34a', withdrawal: '#dc2626' };
    const btn = document.getElementById('bbtn-' + type);
    btn.style.borderColor = colors[type];
    btn.style.color = colors[type];
    btn.style.background = colors[type] + '15';
    document.getElementById('bsubmitBtn').style.background = colors[type];
}

function selectBCurrency(code) {
    ['USD','IQD','EUR','TRY'].forEach(c => {
        const el = document.getElementById('bcur-' + c);
        if(el) { el.style.borderColor = '#e0e0e0'; el.style.color = '#444'; el.style.background = 'white'; }
    });
    const el = document.getElementById('bcur-' + code);
    if(el) { el.style.borderColor = '#1a3c5e'; el.style.color = '#1a3c5e'; el.style.background = '#e8f0fe'; }
}

selectBType('deposit');
selectBCurrency('USD');
</script>
@endsection