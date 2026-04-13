@extends('layouts.app')
@section('title', $client->full_name)
@section('content')

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 16px;">
        <a href="{{ route('clients.index') }}" class="btn-secondary">← {{ $trans['back'] }}</a>
        <div class="page-title">
            <h1>{{ $client->full_name }}</h1>
            <p>{{ $client->code }} · {{ $client->phone }}</p>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <button onclick="window.print()" class="btn-secondary">🖨️ {{ $trans['print_statement'] }}</button>
        <button onclick="document.getElementById('txModal').style.display='flex'" class="btn-primary">+ {{ $trans['add_transaction'] }}</button>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 20px;">
    <!-- Client Info -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="card card-body">
            <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e; margin-bottom: 16px;">{{ $trans['client_info'] }}</h2>
            @foreach([
                [$trans['client_code'], $client->code],
                [$trans['status'], $client->status],
                [$trans['phone'], $client->phone],
                [$trans['address'], $client->address],
                [$trans['national_id'], $client->national_id],
                [$trans['member_since'], $client->created_at->format('Y-m-d')],
            ] as [$label, $value])
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f5f5f5;">
                <span style="font-size: 13px; color: #666;">{{ $label }}</span>
                <span style="font-size: 13px; font-weight: 500;">{{ $value }}</span>
            </div>
            @endforeach
            <div style="margin-top: 12px; padding: 10px; background: #f8f9fa; border-radius: 8px; font-size: 12px; color: #666; line-height: 1.8;">
                📈 {{ $trans['current_rates'] }}:<br>
                1 USD = {{ number_format($rates['IQD'] ?? 0, 0) }} IQD<br>
                1 USD = {{ number_format($rates['EUR'] ?? 0, 4) }} EUR<br>
                1 USD = {{ number_format($rates['TRY'] ?? 0, 2) }} TRY
            </div>
        </div>

        <!-- Portal Access -->
        @if(!$client->login_enabled)
        <div class="card card-body" style="border-left: 4px solid #2563eb;">
            <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e; margin-bottom: 16px;">{{ $trans['enable_portal'] }}</h2>
            <form method="POST" action="{{ route('clients.enable-login', $client->id) }}">
                @csrf
                <div class="form-group">
                    <label>{{ $trans['email'] }}</label>
                    <input type="email" name="email" class="form-control" placeholder="client@email.com" value="{{ $client->email }}">
                </div>
                <div class="form-group">
                    <label>{{ $trans['phone'] }}</label>
                    <input type="text" name="phone" class="form-control" value="{{ $client->phone }}">
                </div>
                <div class="form-group">
                    <label>{{ $trans['set_password'] }}</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">{{ $trans['enable_access'] }}</button>
            </form>
        </div>
        @else
        <div class="card card-body" style="border-left: 4px solid #16a34a;">
            <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e; margin-bottom: 12px;">{{ $trans['portal_access'] }}</h2>
            <div style="color: #16a34a; font-weight: 600; margin-bottom: 12px;">✓ {{ $trans['portal_enabled'] }}</div>
            <div style="font-size: 13px; color: #666; margin-bottom: 16px;">
                Login: {{ $client->email ?? $client->phone }}<br>
                URL: <a href="{{ route('portal.login') }}" target="_blank" style="color: #1a3c5e;">{{ route('portal.login') }}</a>
            </div>
            <form method="POST" action="{{ route('clients.disable-login', $client->id) }}">
                @csrf
                <button type="submit" class="btn-secondary" style="width: 100%; color: #dc2626; border-color: #dc2626;">{{ $trans['disable_portal'] }}</button>
            </form>
        </div>
        @endif
    </div>

    <!-- Balances -->
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($balances as $code => $b)
        <div class="stat-card" style="border-left: 4px solid {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }}; border-right: 4px solid {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }}; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div class="label">{{ $trans[strtolower($code) . '_balance'] }}</div>
                <div class="value" style="color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                    {{ $b['symbol'] }} {{ number_format($b['balance'], $code === 'IQD' ? 0 : 2) }}
                </div>
            </div>
            <div style="padding: 8px 14px; border-radius: 8px; background: {{ $b['balance'] < 0 ? '#fef2f2' : '#f0fdf4' }}; font-size: 13px; font-weight: 600; color: {{ $b['balance'] < 0 ? '#dc2626' : $b['color'] }};">
                {{ $b['balance'] < 0 ? $trans['owes_company'] : ($b['balance'] == 0 ? $trans['zero_balance'] : $trans['has_funds']) }}
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Transactions -->
<div class="card">
    <div style="padding: 20px 24px; border-bottom: 1px solid #f0f0f0;">
        <h2 style="font-size: 15px; font-weight: 600; color: #1a3c5e;">{{ $trans['transaction_history'] }}</h2>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ $trans['date'] }}</th>
                <th>{{ $trans['type'] }}</th>
                <th>{{ $trans['original'] }}</th>
                <th>USD</th>
                <th>IQD</th>
                <th>EUR</th>
                <th>TRY</th>
                <th>{{ $trans['sender'] }}</th>
                <th>{{ $trans['notes'] }}</th>
                <th>{{ $trans['cashier'] }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($client->transactions->sortByDesc('created_at') as $tx)
            <tr>
                <td style="color: #666; font-size: 12px;">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <span class="badge {{ $tx->type === 'deposit' ? 'badge-success' : 'badge-danger' }}">
                        {{ $trans[$tx->type] }}
                    </span>
                </td>
                <td style="font-weight: 600; color: #1a3c5e;">
                    {{ $tx->original_currency }} {{ number_format($tx->original_amount, 2) }}
                </td>
                <td class="{{ $tx->type === 'withdrawal' ? 'amount-negative' : 'amount-positive' }}">
                    $ {{ number_format($tx->amount, 2) }}
                </td>
                <td style="color: #2563eb;">IQD {{ number_format($tx->amount * ($rates['IQD'] ?? 0), 0) }}</td>
                <td style="color: #d97706;">€ {{ number_format($tx->amount * ($rates['EUR'] ?? 0), 2) }}</td>
                <td style="color: #7c3aed;">₺ {{ number_format($tx->amount * ($rates['TRY'] ?? 0), 2) }}</td>
                <td style="font-size: 12px; color: #666;">
                    {{ $tx->sender_name ?? '—' }}<br>
                    <span style="color: #999;">{{ $tx->sender_phone ?? '' }}</span>
                </td>
                <td style="color: #666; font-size: 12px;">{{ $tx->notes ?? '—' }}</td>
                <td style="color: #666; font-size: 12px;">{{ $tx->createdBy->name }}</td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align: center; color: #999; padding: 40px;">{{ $trans['no_transactions'] }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Add Transaction Modal -->
<div id="txModal" class="modal-overlay" style="display: none;">
    <div class="modal" style="width: 500px;">
        <h2>{{ $trans['add_transaction'] }}</h2>
        <p style="font-size: 13px; color: #666; margin-bottom: 24px; margin-top: -16px;">{{ $client->full_name }}</p>
        <form method="POST" action="{{ route('transactions.store') }}">
            @csrf
            <input type="hidden" name="client_id" value="{{ $client->id }}">

            <div class="form-group">
                <label>{{ $trans['type'] }}</label>
                <div style="display: flex; gap: 8px;">
                    <label style="flex:1; text-align:center; padding:10px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="btn-deposit">
                        <input type="radio" name="type" value="deposit" style="display:none;" onchange="selectType('deposit')" checked>
                        ↓ {{ $trans['deposit'] }}
                    </label>
                    <label style="flex:1; text-align:center; padding:10px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="btn-withdrawal">
                        <input type="radio" name="type" value="withdrawal" style="display:none;" onchange="selectType('withdrawal')">
                        ↑ {{ $trans['withdrawal'] }}
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['currency'] }}</label>
                <div style="display: flex; gap: 8px;">
                    @foreach(['USD' => '$', 'IQD' => 'IQD', 'EUR' => '€', 'TRY' => '₺'] as $code => $symbol)
                    <label style="flex:1; text-align:center; padding:8px; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600;" id="cur-{{ $code }}">
                        <input type="radio" name="currency_code" value="{{ $code }}" style="display:none;" onchange="selectCurrency('{{ $code }}')" {{ $code === 'USD' ? 'checked' : '' }}>
                        {{ $symbol }} {{ $code }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['amount'] }}</label>
                <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required oninput="calcEquivalents()">
            </div>

            <div style="background: #f8f9fa; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
                <div style="color: #666; margin-bottom: 6px; font-weight: 500;">{{ $trans['equivalent'] }}:</div>
                <div style="font-size: 18px; font-weight: 700; color: #1a3c5e;">$ <span id="usdEquiv">0.00</span></div>
                <div style="margin-top: 8px; color: #666; font-size: 12px; line-height: 1.8;">
                    IQD <span id="iqdEquiv">0</span> &nbsp;|&nbsp;
                    € <span id="eurEquiv">0.00</span> &nbsp;|&nbsp;
                    ₺ <span id="tryEquiv">0.00</span>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>{{ $trans['sender_name'] }}</label>
                    <input type="text" name="sender_name" class="form-control" placeholder="{{ $trans['sender_name'] }}">
                </div>
                <div class="form-group">
                    <label>{{ $trans['sender_phone'] }}</label>
                    <input type="text" name="sender_phone" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>{{ $trans['notes_optional'] }}</label>
                <input type="text" name="notes" class="form-control">
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="button" onclick="document.getElementById('txModal').style.display='none'" class="btn-secondary" style="flex:1; text-align:center;">{{ $trans['cancel'] }}</button>
                <button type="submit" id="submitBtn" class="btn-primary" style="flex:1; text-align:center; background:#16a34a;">{{ $trans['confirm_save'] }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const rates = {
    USD: 1,
    IQD: {{ $rates['IQD'] ?? 0 }},
    EUR: {{ $rates['EUR'] ?? 0 }},
    TRY: {{ $rates['TRY'] ?? 0 }},
};

let selectedCurrency = 'USD';

function selectType(type) {
    ['deposit','withdrawal'].forEach(t => {
        const btn = document.getElementById('btn-' + t);
        btn.style.borderColor = '#e0e0e0';
        btn.style.color = '#444';
        btn.style.background = 'white';
    });
    const colors = { deposit: '#16a34a', withdrawal: '#dc2626' };
    const btn = document.getElementById('btn-' + type);
    btn.style.borderColor = colors[type];
    btn.style.color = colors[type];
    btn.style.background = colors[type] + '15';
    document.getElementById('submitBtn').style.background = colors[type];
}

function selectCurrency(code) {
    selectedCurrency = code;
    ['USD','IQD','EUR','TRY'].forEach(c => {
        const el = document.getElementById('cur-' + c);
        el.style.borderColor = '#e0e0e0';
        el.style.color = '#444';
        el.style.background = 'white';
    });
    const el = document.getElementById('cur-' + code);
    el.style.borderColor = '#1a3c5e';
    el.style.color = '#1a3c5e';
    el.style.background = '#e8f0fe';
    calcEquivalents();
}

function calcEquivalents() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    let usdAmount = 0;
    if (selectedCurrency === 'USD') {
        usdAmount = amount;
    } else if (rates[selectedCurrency] > 0) {
        usdAmount = amount / rates[selectedCurrency];
    }
    document.getElementById('usdEquiv').textContent = usdAmount.toFixed(2);
    document.getElementById('iqdEquiv').textContent = Math.round(usdAmount * rates.IQD).toLocaleString();
    document.getElementById('eurEquiv').textContent = (usdAmount * rates.EUR).toFixed(2);
    document.getElementById('tryEquiv').textContent = (usdAmount * rates.TRY).toFixed(2);
}

selectType('deposit');
selectCurrency('USD');
</script>
@endsection