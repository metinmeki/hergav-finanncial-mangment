@extends('layouts.app')
@section('title', $client->full_name . ' - ' . ($isRTL ? 'الأمانات' : 'Bailment'))
@section('content')

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('bailment.index') }}" class="btn-secondary">← {{ $trans['back'] }}</a>
        <div class="page-title">
            <h1>{{ $client->full_name }}</h1>
            <p>{{ $client->code }} · {{ $isRTL ? 'حساب الأمانات' : 'Bailment Account' }}</p>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('clients.show', $client->id) }}" class="btn-secondary">
            👤 {{ $isRTL ? 'الحساب العادي' : 'Regular Account' }}
        </a>
        <button onclick="window.print()" class="btn-secondary">🖨️ {{ $trans['print_statement'] }}</button>
        <button onclick="document.getElementById('txModal').style.display='flex'" class="btn-primary">
            + {{ $isRTL ? 'إضافة أمانة' : 'Add Bailment' }}
        </button>
    </div>
</div>

<!-- Bailment Balances -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    @foreach($balances as $code => $b)
    <div class="stat-card" style="border-top: 4px solid {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
        <div class="label">{{ $code }} {{ $isRTL ? 'أمانة' : 'Bailment' }}</div>
        <div class="value" style="color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
            {{ $b['symbol'] }} {{ number_format($b['balance'], $code === 'IQD' ? 0 : 2) }}
        </div>
        <div style="margin-top: 8px;">
            <span style="font-size: 12px; padding: 3px 8px; border-radius: 6px;
                background: {{ $b['balance'] < 0 ? '#fef2f2' : ($b['balance'] == 0 ? '#f5f5f5' : '#f0fdf4') }};
                color: {{ $b['balance'] < 0 ? '#dc2626' : ($b['balance'] == 0 ? '#999' : $b['color']) }};">
                {{ $b['balance'] < 0 ? ($isRTL ? '⚠ مدين' : '⚠ Debt') : ($b['balance'] == 0 ? ($isRTL ? 'صفر' : 'Zero') : ($isRTL ? '✓ أمانة' : '✓ Held')) }}
            </span>
        </div>
    </div>
    @endforeach
</div>

<!-- Transaction History -->
<div class="card">
    <div style="padding: 20px 24px; border-bottom: 1px solid #f0f0f0;">
        <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e;">
            {{ $isRTL ? 'سجل معاملات الأمانات' : 'Bailment Transaction History' }}
        </h2>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ $trans['date'] }}</th>
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
                    <span class="badge {{ $tx->type === 'deposit' ? 'badge-success' : 'badge-danger' }}">
                        {{ $trans[$tx->type] }}
                    </span>
                </td>
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
                <td colspan="10" style="text-align: center; color: #999; padding: 40px;">
                    {{ $isRTL ? 'لا توجد معاملات أمانات بعد' : 'No bailment transactions yet.' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Add Transaction Modal -->
<div id="txModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="width: 500px;">
        <h2>{{ $isRTL ? 'إضافة أمانة' : 'Add Bailment Transaction' }}</h2>
        <p style="font-size: 13px; color: #666; margin-bottom: 24px; margin-top: -16px;">{{ $client->full_name }}</p>
        <form method="POST" action="{{ route('bailment.store') }}">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <div class="form-group">
                <label>{{ $trans['type'] }}</label>
                <div style="display: flex; gap: 8px;">
                    <label style="flex:1; text-align:center; padding:10px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="cbtn-deposit">
                        <input type="radio" name="type" value="deposit" style="display:none;" onchange="selectCType('deposit')" checked>
                        ↓ {{ $trans['deposit'] }}
                    </label>
                    <label style="flex:1; text-align:center; padding:10px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="cbtn-withdrawal">
                        <input type="radio" name="type" value="withdrawal" style="display:none;" onchange="selectCType('withdrawal')">
                        ↑ {{ $trans['withdrawal'] }}
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['currency'] }}</label>
                <div style="display: flex; gap: 8px;">
                    @foreach($currencies as $currency)
                    <label style="flex:1; text-align:center; padding:8px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="ccur-{{ $currency->code }}">
                        <input type="radio" name="currency_code" value="{{ $currency->code }}" style="display:none;" onchange="selectCCurrency('{{ $currency->code }}')" {{ $currency->code === 'USD' ? 'checked' : '' }}>
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
                <button type="button" onclick="document.getElementById('txModal').style.display='none'" class="btn-secondary" style="flex:1; text-align:center;">{{ $trans['cancel'] }}</button>
                <button type="submit" id="csubmitBtn" class="btn-primary" style="flex:1; text-align:center; background:#16a34a;">{{ $trans['confirm_save'] }}</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function selectCType(type) {
    ['deposit','withdrawal'].forEach(t => {
        const btn = document.getElementById('cbtn-' + t);
        btn.style.borderColor = '#e0e0e0';
        btn.style.color = '#444';
        btn.style.background = 'white';
    });
    const colors = { deposit: '#16a34a', withdrawal: '#dc2626' };
    const btn = document.getElementById('cbtn-' + type);
    btn.style.borderColor = colors[type];
    btn.style.color = colors[type];
    btn.style.background = colors[type] + '15';
    document.getElementById('csubmitBtn').style.background = colors[type];
}

function selectCCurrency(code) {
    ['USD','IQD','EUR','TRY'].forEach(c => {
        const el = document.getElementById('ccur-' + c);
        if(el) { el.style.borderColor = '#e0e0e0'; el.style.color = '#444'; el.style.background = 'white'; }
    });
    const el = document.getElementById('ccur-' + code);
    if(el) { el.style.borderColor = '#1a3c5e'; el.style.color = '#1a3c5e'; el.style.background = '#e8f0fe'; }
}

selectCType('deposit');
selectCCurrency('USD');
</script>
@endsection